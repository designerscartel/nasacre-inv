<?php

namespace Nasacre\ReHubs;

class ReHubApi
{
    private const CACHE_TTL = 12 * MINUTE_IN_SECONDS;

    public function entries($type, array $filters = [])
    {
        if ($type === 'all') {
            return $this->mergeById(
                $this->entries('places', $filters),
                $this->entries('speakers', $filters)
            );
        }

        $endpoint = $type === 'speakers' ? 'school-speakers' : 'places-of-interest';

        return $this->request($endpoint, array_filter([
            'search' => $filters['search'] ?? '',
            'region' => $filters['region'] ?? '',
        ], static fn ($value) => $value !== ''));
    }

    public function regions($type)
    {
        if ($type === 'all') {
            return $this->mergeRegions(
                $this->regions('places'),
                $this->regions('speakers')
            );
        }

        $endpoint = $type === 'speakers'
            ? 'regions/school-speakers'
            : 'regions/places-of-interest';

        return $this->request($endpoint);
    }

    private function request($endpoint, array $query = [])
    {
        $baseUrl = untrailingslashit(apply_filters(
            'nasacre_re_hubs_api_url',
            'https://database.nasacre.org.uk/api/re-hubs'
        ));
        $url = $baseUrl . '/' . ltrim($endpoint, '/');

        if ($query) {
            $url = add_query_arg($query, $url);
        }

        $cacheKey = 'nasacre_re_hubs_v1_' . md5($url);
        $cached = get_transient($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $response = wp_remote_get($url, [
            'timeout' => 15,
            'sslverify' => true,
            'headers' => ['Accept' => 'application/json'],
        ]);

        if (is_wp_error($response)) {
            $this->logError($response->get_error_message());
            return [];
        }

        $status = wp_remote_retrieve_response_code($response);
        if ($status < 200 || $status >= 300) {
            $this->logError("API returned HTTP {$status} for {$endpoint}.");
            return [];
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (! is_array($data) || ! isset($data['data']) || ! is_array($data['data'])) {
            $this->logError("API returned invalid JSON for {$endpoint}.");
            return [];
        }

        set_transient($cacheKey, $data['data'], self::CACHE_TTL);

        return $data['data'];
    }

    private function mergeById(array $first, array $second)
    {
        $entries = [];
        foreach (array_merge($first, $second) as $entry) {
            $entries[absint($entry['id'] ?? 0)] = $entry;
        }

        $entries = array_values(array_filter($entries, static fn ($entry) => ! empty($entry['id'])));
        usort($entries, static fn ($firstEntry, $secondEntry) => strcasecmp(
            $firstEntry['name'] ?? '',
            $secondEntry['name'] ?? ''
        ));

        return $entries;
    }

    private function mergeRegions(array $first, array $second)
    {
        $regions = [];
        foreach (array_merge($first, $second) as $region) {
            $id = absint($region['id'] ?? 0);
            if (! $id) {
                continue;
            }

            $regions[$id] = [
                'id' => $id,
                'title' => $region['title'] ?? '',
            ];
        }

        $regions = array_values($regions);
        usort($regions, static fn ($firstRegion, $secondRegion) => strcasecmp(
            $firstRegion['title'],
            $secondRegion['title']
        ));

        return $regions;
    }

    private function logError($message)
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('NASACRE RE Hubs: ' . $message);
        }
    }
}
