<?php
/**
 * Plugin Name: NASACRE RE Hubs
 * Description: Provides a searchable RE Hubs directory with the [re_hubs] shortcode.
 * Version: 1.0.0
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

function nasacre_re_hubs_enqueue_assets()
{
    global $post;

    if (! $post instanceof WP_Post || ! has_shortcode($post->post_content, 're_hubs')) {
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
