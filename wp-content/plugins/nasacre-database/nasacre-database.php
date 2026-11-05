<?php
/**
 * Plugin Name: Nasacre Database
 * Description: Provides a searchable SACRE directory with the [database_items] shortcode.
 * Version: 1.1.0
 * Author: Matt Tullett
 */

use inc\Database;

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'inc/Database.php';

function nasacre_database_shortcode($atts)
{
    $search = isset($_GET['search'])
        ? sanitize_text_field(wp_unslash($_GET['search']))
        : '';
    $sacres = (new Database())->getSacres($search);
    $reportApiUrl = untrailingslashit(apply_filters(
        'nasacre_database_report_api_url',
        'https://database.nasacre.org.uk/api/database'
    ));
    $memberIconUrl = apply_filters(
        'nasacre_database_member_icon_url',
        get_theme_file_uri('assets/img/flower.svg')
    );
    $pastMemberIconUrl = apply_filters(
        'nasacre_database_past_member_icon_url',
        get_theme_file_uri('assets/img/flower-gray.svg')
    );

    ob_start();
    ?>
    <div class="nasacre-database-search">
        <form method="get" action="<?php echo esc_url(home_url('/database/')); ?>">
            <div class="search-form">
                <label class="screen-reader-text" for="nasacre-database-search">Search the SACRE database</label>
                <input
                    id="nasacre-database-search"
                    name="search"
                    class="search-input"
                    type="search"
                    value="<?php echo esc_attr($search); ?>"
                    placeholder="Search Database"
                >
                <button type="submit" class="submit" aria-label="Search database">
                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <?php if ($sacres) : ?>
        <div class="nasacre-database-results">
            <?php foreach ($sacres as $sacre) : ?>
                <?php
                $sacreId = absint($sacre['id'] ?? 0);
                $panelId = 'nasacre-sacre-' . $sacreId;
                $contacts = is_array($sacre['contacts'] ?? null) ? $sacre['contacts'] : [];
                $files = is_array($sacre['files'] ?? null) ? $sacre['files'] : [];
                $positions = [1 => 'Chair', 2 => 'Clerk', 3 => 'LA', 4 => 'Adviser'];
                ?>
                <div class="result">
                    <div
                        class="result-header"
                        role="button"
                        tabindex="0"
                        aria-expanded="false"
                        aria-controls="<?php echo esc_attr($panelId); ?>"
                    >
                        <div class="details">
                            <p class="region"><?php echo esc_html($sacre['region']['title'] ?? ''); ?></p>
                            <h3><?php echo esc_html($sacre['title'] ?? ''); ?></h3>
                        </div>

                        <?php if (! empty($sacre['member'])) : ?>
                            <div class="member">
                                <img alt="Current NASACRE member" src="<?php echo esc_url($memberIconUrl); ?>">
                            </div>
                        <?php elseif (! empty($sacre['past_member'])) : ?>
                            <div class="member past">
                                <img alt="Past NASACRE member" src="<?php echo esc_url($pastMemberIconUrl); ?>">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="result-body" id="<?php echo esc_attr($panelId); ?>" hidden>
                        <?php if ($contacts) : ?>
                            <div class="contacts">
                                <h3>Contacts</h3>
                                <ul>
                                    <?php foreach ($contacts as $contact) : ?>
                                        <?php
                                        $position = is_array($contact['position'] ?? null) ? $contact['position'] : [];
                                        $positionId = absint($position['id'] ?? 0);
                                        $positionTitle = (string) ($position['title'] ?? 'Contact');
                                        $email = sanitize_email($contact['email'] ?? '');
                                        $hideEmail = in_array(strtolower($positionTitle), ['chair', 'advisor', 'adviser'], true);
                                        unset($positions[$positionId]);
                                        ?>
                                        <li class="flex justify-between border-b border-blue-900 py-2">
                                            <p><strong><?php echo esc_html($positionTitle); ?></strong> <?php echo esc_html($contact['name'] ?? ''); ?></p>
                                            <?php if (! $hideEmail && is_email($email)) : ?>
                                                <p><a class="cursor-pointer hover:text-orange-900" href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>

                                    <?php foreach ($positions as $position) : ?>
                                        <li class="flex justify-between border-b border-blue-900 py-2"><p><strong><?php echo esc_html($position); ?></strong></p></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($files) : ?>
                            <div class="files">
                                <h3 class="text-2xl mb-4">Reports</h3>
                                <ul class="border-t border-blue-900">
                                    <?php foreach ($files as $file) : ?>
                                        <?php $reportUrl = $reportApiUrl . '/' . $sacreId . '/files/' . absint($file['id'] ?? 0); ?>
                                        <li class="flex justify-between border-b border-blue-900 py-2">
                                            <a target="_blank" rel="noopener noreferrer" class="block w-full cursor-pointer hover:text-orange-900" href="<?php echo esc_url($reportUrl); ?>">
                                                View <?php echo esc_html($file['year'] ?? ''); ?> Report
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($search !== '') : ?>
        <p class="nasacre-database-empty">No SACREs matched your search.</p>
    <?php endif; ?>
    <?php

    return ob_get_clean();
}
add_shortcode('database_items', 'nasacre_database_shortcode');

function nasacre_database_enqueue_assets()
{
    global $post;

    if (! $post instanceof WP_Post || ! has_shortcode($post->post_content, 'database_items')) {
        return;
    }

    $cssPath = plugin_dir_path(__FILE__) . 'assets/css/database.css';
    $jsPath = plugin_dir_path(__FILE__) . 'assets/js/database.js';

    wp_enqueue_style(
        'nasacre-database-style',
        plugin_dir_url(__FILE__) . 'assets/css/database.css',
        [],
        (string) filemtime($cssPath)
    );
    wp_enqueue_script(
        'nasacre-database-script',
        plugin_dir_url(__FILE__) . 'assets/js/database.js',
        [],
        (string) filemtime($jsPath),
        true
    );
}
add_action('wp_enqueue_scripts', 'nasacre_database_enqueue_assets');
