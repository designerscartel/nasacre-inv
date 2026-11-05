<?php
/**
 * Plugin Name: NASACRE RE Hubs
 * Description: Provides searchable RE Hubs directories with the [re_hubs] and [re_hub_regions] shortcodes.
 * Version: 1.2.0
 * Author: Matt Tullett
 */

use Nasacre\ReHubs\ReHubApi;

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'inc/ReHubApi.php';

function nasacre_re_hubs_shortcode($atts)
{
    $atts = shortcode_atts([
        'type' => 'all',
        'show_filters' => 'true',
    ], $atts, 're_hubs');

    $allowedTypes = ['all', 'places', 'speakers'];
    $defaultType = in_array($atts['type'], $allowedTypes, true) ? $atts['type'] : 'all';
    $requestedType = isset($_GET['re_hub_type'])
        ? sanitize_key(wp_unslash($_GET['re_hub_type']))
        : $defaultType;
    $type = in_array($requestedType, $allowedTypes, true) ? $requestedType : $defaultType;
    $search = isset($_GET['re_hub_search'])
        ? sanitize_text_field(wp_unslash($_GET['re_hub_search']))
        : '';
    $region = isset($_GET['re_hub_region']) ? absint(wp_unslash($_GET['re_hub_region'])) : 0;
    $showFilters = filter_var($atts['show_filters'], FILTER_VALIDATE_BOOLEAN);

    $api = new ReHubApi();
    $regions = $api->regions($type);
    if ($region && ! in_array($region, array_map('absint', array_column($regions, 'id')), true)) {
        $region = 0;
    }
    $entries = $api->entries($type, [
        'search' => $search,
        'region' => $region,
    ]);

    ob_start();
    ?>
    <section class="nasacre-re-hubs" aria-labelledby="nasacre-re-hubs-title">
        <header class="nasacre-re-hubs__header">
            <h2 id="nasacre-re-hubs-title">RE Hubs Directory</h2>
            <p>Find places of interest and school speakers by name, religion or belief, contact, or region.</p>
        </header>

        <?php if ($showFilters) : ?>
            <form class="nasacre-re-hubs__filters" method="get" action="<?php echo esc_url(get_permalink()); ?>">
                <div class="nasacre-re-hubs__field nasacre-re-hubs__field--search">
                    <label for="re-hub-search">Search</label>
                    <input id="re-hub-search" type="search" name="re_hub_search" value="<?php echo esc_attr($search); ?>" placeholder="Name, religion, belief, or contact">
                </div>
                <div class="nasacre-re-hubs__field">
                    <label for="re-hub-type">Directory type</label>
                    <select id="re-hub-type" name="re_hub_type">
                        <option value="all" <?php selected($type, 'all'); ?>>All entries</option>
                        <option value="places" <?php selected($type, 'places'); ?>>Places of interest</option>
                        <option value="speakers" <?php selected($type, 'speakers'); ?>>School speakers</option>
                    </select>
                </div>
                <div class="nasacre-re-hubs__field">
                    <label for="re-hub-region">Region</label>
                    <select id="re-hub-region" name="re_hub_region">
                        <option value="">All regions</option>
                        <?php foreach ($regions as $regionOption) : ?>
                            <option value="<?php echo absint($regionOption['id'] ?? 0); ?>" <?php selected($region, absint($regionOption['id'] ?? 0)); ?>>
                                <?php echo esc_html($regionOption['title'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="nasacre-re-hubs__actions">
                    <button type="submit">Search</button>
                    <a href="<?php echo esc_url(get_permalink()); ?>">Clear filters</a>
                </div>
            </form>
        <?php endif; ?>

        <p class="nasacre-re-hubs__count" aria-live="polite">
            <?php echo esc_html(sprintf(_n('%d result', '%d results', count($entries), 'nasacre-re-hubs'), count($entries))); ?>
        </p>

        <?php if ($entries) : ?>
            <div class="nasacre-re-hubs__results">
                <?php foreach ($entries as $entry) : ?>
                    <?php
                    $entryRegions = is_array($entry['regions'] ?? null) ? $entry['regions'] : [];
                    $regionNames = array_filter(array_column($entryRegions, 'title'));
                    $website = esc_url($entry['website'] ?? '');
                    $email = sanitize_email($entry['organisation_email'] ?? '');
                    ?>
                    <details class="nasacre-re-hubs__entry">
                        <summary>
                            <span class="nasacre-re-hubs__entry-name"><?php echo esc_html($entry['name'] ?? ''); ?></span>
                            <span class="nasacre-re-hubs__badges">
                                <?php if (! empty($entry['is_place_of_interest'])) : ?>
                                    <span class="nasacre-re-hubs__badge nasacre-re-hubs__badge--place">Place of interest</span>
                                <?php endif; ?>
                                <?php if (! empty($entry['is_school_speaker'])) : ?>
                                    <span class="nasacre-re-hubs__badge nasacre-re-hubs__badge--speaker">School speaker</span>
                                <?php endif; ?>
                            </span>
                        </summary>
                        <dl class="nasacre-re-hubs__details">
                            <?php if (! empty($entry['religion'])) : ?>
                                <div><dt>Religion or belief</dt><dd><?php echo esc_html($entry['religion']); ?></dd></div>
                            <?php endif; ?>
                            <?php if ($regionNames) : ?>
                                <div><dt>Regions</dt><dd><?php echo esc_html(implode(', ', $regionNames)); ?></dd></div>
                            <?php endif; ?>
                            <?php if (! empty($entry['contact_name'])) : ?>
                                <div><dt>Contact</dt><dd><?php echo esc_html($entry['contact_name']); ?></dd></div>
                            <?php endif; ?>
                            <?php if (is_email($email)) : ?>
                                <div><dt>Email</dt><dd><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></dd></div>
                            <?php endif; ?>
                            <?php if (! empty($entry['phone'])) : ?>
                                <div><dt>Phone</dt><dd><?php echo esc_html($entry['phone']); ?></dd></div>
                            <?php endif; ?>
                            <?php if ($website) : ?>
                                <div><dt>Website</dt><dd><a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer">Visit website</a></dd></div>
                            <?php endif; ?>
                        </dl>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="nasacre-re-hubs__empty">No RE Hub entries matched your filters.</p>
        <?php endif; ?>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode('re_hubs', 'nasacre_re_hubs_shortcode');

function nasacre_re_hub_regions_shortcode($atts)
{
    $atts = shortcode_atts([
        'type' => 'all',
        'show_filters' => 'true',
        'show_type_filter' => 'true',
        'show_type_badges' => 'true',
        'search_mode' => 'entries',
    ], $atts, 're_hub_regions');

    $allowedTypes = ['all', 'places', 'speakers'];
    $defaultType = in_array($atts['type'], $allowedTypes, true) ? $atts['type'] : 'all';
    $showFilters = filter_var($atts['show_filters'], FILTER_VALIDATE_BOOLEAN);
    $showTypeFilter = $showFilters && filter_var($atts['show_type_filter'], FILTER_VALIDATE_BOOLEAN);
    $showTypeBadges = filter_var($atts['show_type_badges'], FILTER_VALIDATE_BOOLEAN);
    $allowedSearchModes = ['none', 'entries', 'regions'];
    $searchMode = in_array($atts['search_mode'], $allowedSearchModes, true)
        ? $atts['search_mode']
        : 'entries';
    $requestedType = $showTypeFilter && isset($_GET['re_hub_type'])
        ? sanitize_key(wp_unslash($_GET['re_hub_type']))
        : $defaultType;
    $type = in_array($requestedType, $allowedTypes, true) ? $requestedType : $defaultType;
    $region = isset($_GET['re_hub_region']) ? absint(wp_unslash($_GET['re_hub_region'])) : 0;
    $search = isset($_GET['re_hub_search'])
        ? sanitize_text_field(wp_unslash($_GET['re_hub_search']))
        : '';
    $api = new ReHubApi();
    $regions = $api->regions($type, ! $region && $searchMode === 'regions' ? $search : '');
    $selectedRegion = null;

    if ($region) {
        foreach ($api->regions($type) as $regionOption) {
            if (absint($regionOption['id'] ?? 0) === $region) {
                $selectedRegion = $regionOption;
                break;
            }
        }
    }

    if ($region && ! $selectedRegion) {
        $region = 0;
        $search = '';
    }

    if (! $region && $search !== '' && $searchMode === 'entries') {
        $matchingEntries = $api->entries($type, ['search' => $search]);
        $matchingRegionIds = [];

        foreach ($matchingEntries as $matchingEntry) {
            foreach (($matchingEntry['regions'] ?? []) as $matchingRegion) {
                $matchingRegionIds[] = absint($matchingRegion['id'] ?? 0);
            }
        }

        $matchingRegionIds = array_unique(array_filter($matchingRegionIds));
        $regions = array_values(array_filter($regions, static fn ($regionOption) => in_array(
            absint($regionOption['id'] ?? 0),
            $matchingRegionIds,
            true
        )));
    }

    $entries = $region ? $api->entries($type, [
        'search' => $searchMode === 'entries' ? $search : '',
        'region' => $region,
    ]) : [];
    $instanceId = wp_unique_id('nasacre-re-hub-regions-');
    $baseUrl = get_permalink();
    $backArgs = ['re_hub_type' => $type];
    if ($searchMode === 'entries' && $search !== '') {
        $backArgs['re_hub_search'] = $search;
    }
    $showSearch = $showFilters
        && $searchMode !== 'none'
        && (! $selectedRegion || $searchMode === 'entries');

    ob_start();
    ?>
    <section class="nasacre-re-hubs nasacre-re-hubs--regions" aria-labelledby="<?php echo esc_attr($instanceId); ?>-title">
        <header class="nasacre-re-hubs__header">
            <h2 id="<?php echo esc_attr($instanceId); ?>-title">
                <?php echo $selectedRegion ? esc_html($selectedRegion['title'] ?? '') : 'RE Hubs by Region'; ?>
            </h2>
            <p>
                <?php echo $selectedRegion
                    ? 'Browse RE Hub entries available in this region.'
                    : 'Choose a region to find its places of interest and school speakers.'; ?>
            </p>
        </header>

        <?php if ($selectedRegion) : ?>
            <p class="nasacre-re-hubs__back">
                <a href="<?php echo esc_url(add_query_arg($backArgs, $baseUrl)); ?>">&larr; Back to all regions</a>
            </p>
        <?php endif; ?>

        <?php if ($showFilters) : ?>
            <form class="nasacre-re-hubs__filters nasacre-re-hubs__filters--regions" method="get" action="<?php echo esc_url($baseUrl); ?>">
                <?php if ($selectedRegion) : ?>
                    <input type="hidden" name="re_hub_region" value="<?php echo absint($region); ?>">
                <?php endif; ?>
                <?php if (! $showTypeFilter) : ?>
                    <input type="hidden" name="re_hub_type" value="<?php echo esc_attr($type); ?>">
                <?php endif; ?>
                <?php if ($showSearch) : ?>
                    <div class="nasacre-re-hubs__field nasacre-re-hubs__field--search">
                        <label for="<?php echo esc_attr($instanceId); ?>-search"><?php echo $searchMode === 'entries' ? 'Search entries' : 'Search regions'; ?></label>
                        <input id="<?php echo esc_attr($instanceId); ?>-search" type="search" name="re_hub_search" value="<?php echo esc_attr($search); ?>" placeholder="<?php echo $searchMode === 'entries' ? 'Name, religion, belief, or contact' : 'Region name'; ?>">
                    </div>
                <?php endif; ?>
                <?php if ($showTypeFilter) : ?>
                    <div class="nasacre-re-hubs__field">
                        <label for="<?php echo esc_attr($instanceId); ?>-type">Directory type</label>
                        <select id="<?php echo esc_attr($instanceId); ?>-type" name="re_hub_type">
                            <option value="all" <?php selected($type, 'all'); ?>>All entries</option>
                            <option value="places" <?php selected($type, 'places'); ?>>Places of interest</option>
                            <option value="speakers" <?php selected($type, 'speakers'); ?>>School speakers</option>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="nasacre-re-hubs__actions">
                    <button type="submit">Search</button>
                    <a href="<?php echo esc_url($selectedRegion
                        ? add_query_arg(['re_hub_region' => $region, 're_hub_type' => $type], $baseUrl)
                        : add_query_arg(['re_hub_type' => $type], $baseUrl)); ?>">Clear search</a>
                </div>
            </form>
        <?php endif; ?>

        <?php if (! $selectedRegion) : ?>
            <?php if ($regions) : ?>
                <nav class="nasacre-re-hubs__region-grid" aria-label="RE Hub regions">
                    <?php foreach ($regions as $regionOption) : ?>
                        <?php
                        $regionUrlArgs = [
                            're_hub_region' => absint($regionOption['id'] ?? 0),
                            're_hub_type' => $type,
                        ];
                        if ($searchMode === 'entries' && $search !== '') {
                            $regionUrlArgs['re_hub_search'] = $search;
                        }
                        ?>
                        <a class="nasacre-re-hubs__region" href="<?php echo esc_url(add_query_arg($regionUrlArgs, $baseUrl)); ?>">
                            <span><?php echo esc_html($regionOption['title'] ?? ''); ?></span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php else : ?>
                <p class="nasacre-re-hubs__empty">No regions matched your filters.</p>
            <?php endif; ?>
        <?php else : ?>
            <p class="nasacre-re-hubs__count" aria-live="polite">
                <?php echo esc_html(sprintf(_n('%d result', '%d results', count($entries), 'nasacre-re-hubs'), count($entries))); ?>
            </p>
            <?php echo nasacre_re_hubs_render_entries($entries, $showTypeBadges); ?>
        <?php endif; ?>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode('re_hub_regions', 'nasacre_re_hub_regions_shortcode');

function nasacre_re_hubs_render_entries(array $entries, $showTypeBadges = true)
{
    if (! $entries) {
        return '<p class="nasacre-re-hubs__empty">No RE Hub entries matched your filters.</p>';
    }

    ob_start();
    ?>
    <div class="nasacre-re-hubs__results">
        <?php foreach ($entries as $entry) : ?>
            <?php
            $entryRegions = is_array($entry['regions'] ?? null) ? $entry['regions'] : [];
            $regionNames = array_filter(array_column($entryRegions, 'title'));
            $website = esc_url($entry['website'] ?? '');
            $email = sanitize_email($entry['organisation_email'] ?? '');
            ?>
            <details class="nasacre-re-hubs__entry">
                <summary>
                    <span class="nasacre-re-hubs__entry-name"><?php echo esc_html($entry['name'] ?? ''); ?></span>
                    <?php if ($showTypeBadges) : ?>
                        <span class="nasacre-re-hubs__badges">
                            <?php if (! empty($entry['is_place_of_interest'])) : ?>
                                <span class="nasacre-re-hubs__badge nasacre-re-hubs__badge--place">Place of interest</span>
                            <?php endif; ?>
                            <?php if (! empty($entry['is_school_speaker'])) : ?>
                                <span class="nasacre-re-hubs__badge nasacre-re-hubs__badge--speaker">School speaker</span>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </summary>
                <dl class="nasacre-re-hubs__details">
                    <?php if (! empty($entry['religion'])) : ?>
                        <div><dt>Religion or belief</dt><dd><?php echo esc_html($entry['religion']); ?></dd></div>
                    <?php endif; ?>
                    <?php if ($regionNames) : ?>
                        <div><dt>Regions</dt><dd><?php echo esc_html(implode(', ', $regionNames)); ?></dd></div>
                    <?php endif; ?>
                    <?php if (! empty($entry['contact_name'])) : ?>
                        <div><dt>Contact</dt><dd><?php echo esc_html($entry['contact_name']); ?></dd></div>
                    <?php endif; ?>
                    <?php if (is_email($email)) : ?>
                        <div><dt>Email</dt><dd><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></dd></div>
                    <?php endif; ?>
                    <?php if (! empty($entry['phone'])) : ?>
                        <div><dt>Phone</dt><dd><?php echo esc_html($entry['phone']); ?></dd></div>
                    <?php endif; ?>
                    <?php if ($website) : ?>
                        <div><dt>Website</dt><dd><a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer">Visit website</a></dd></div>
                    <?php endif; ?>
                </dl>
            </details>
        <?php endforeach; ?>
    </div>
    <?php

    return ob_get_clean();
}

function nasacre_re_hubs_enqueue_assets()
{
    global $post;

    if (! $post instanceof WP_Post
        || (! has_shortcode($post->post_content, 're_hubs') && ! has_shortcode($post->post_content, 're_hub_regions'))) {
        return;
    }

    $path = plugin_dir_path(__FILE__) . 'assets/css/re-hubs.css';
    wp_enqueue_style(
        'nasacre-re-hubs',
        plugin_dir_url(__FILE__) . 'assets/css/re-hubs.css',
        [],
        (string) filemtime($path)
    );
}
add_action('wp_enqueue_scripts', 'nasacre_re_hubs_enqueue_assets');
