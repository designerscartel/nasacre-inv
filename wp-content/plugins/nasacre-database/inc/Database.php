<?php

namespace inc;

class Database
{
    private const CACHE_TTL = 12 * MINUTE_IN_SECONDS;

    public function getSacres($term = null)
    {
        $term = sanitize_text_field((string) $term);
        $cacheKey = 'nasacre_database_v2_' . md5(strtolower($term));
        $cached = get_transient($cacheKey);

        if (is_array($cached)) {
            return $cached;
        }

        $baseUrl = apply_filters('nasacre_database_api_url', 'https://database.nasacre.org.uk/api/database');
        $url = untrailingslashit($baseUrl);

        if ($term !== '') {
            $url .= '/' . rawurlencode($term);
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
            $this->logError("API returned HTTP {$status}.");
            return [];
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (! is_array($data) || ! isset($data['data']) || ! is_array($data['data'])) {
            $this->logError('API returned an invalid directory response.');
            return [];
        }

        $sacres = $data['data'];
        usort($sacres, fn ($first, $second) => strcasecmp($first['title'] ?? '', $second['title'] ?? ''));
        set_transient($cacheKey, $sacres, self::CACHE_TTL);

        return $sacres;
    }

    private function logError($message)
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('NASACRE Database: ' . $message);
        }
    }
}
