<?php
/**
 * Plugin Name: Nasacre Database
 * Description: Provides a [database_items] shortcode with styles.
 * Version: 1.0
 * Author: Matt Tullett
 */

use inc\Database;

if (!defined('ABSPATH')) exit; // Prevent direct access

// Load your Database class
require_once plugin_dir_path(__FILE__) . 'inc/Database.php';

// Shortcode function
function nasacre_database_shortcode($atts) {

    $database = new Database();
    $sacres = $database->getSacres($_GET['search']);

    // Start output buffering
    $output = '';

    // Search form
    $output .= '<div class="nasacre-database-search">';
    $output .= '<form method="get" action="/database">';
    $output .= '<div class="search-form">';
    $output .= '<input name="search" class="search-input" placeholder="Search Database">';
    $output .= '<button type="submit" id="sacresubmit" class="submit">';
    $output .= '<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">';
    $output .= '<path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />';
    $output .= '</svg>';
    $output .= '</button>';
    $output .= '</form>';
    $output .= '</div>';

    // Results
    if (!empty($sacres)) {
        $output .= '<div class="nasacre-database-results">';

        foreach ($sacres as $key => $sacre) {
            $output .= '<div class="result">';

                $output .= '<div class="result-header">';

                    $output .= '<div class="details">';
                        $output .= '<p class="region">' . esc_html($sacre['region']['title']) . '</p>';
                        $output .= '<h3 class="">' . esc_html($sacre['title']) . '</h3>';
                    $output .= '</div>';

                    if ($sacre['member'] == 1) {
                        $output .= '<div class="member"><img alt="member" src="/wp-content/themes/nasacre/assets/img/flower.svg"></div>';
                    } elseif (!empty($sacre['past_member']) && $sacre['past_member'] == 1) {
                        $output .= '<div class="member past"><img alt="past_member" src="/wp-content/themes/nasacre/assets/img/flower-gray.svg"></div>';
                    }

                $output .= '</div>';


                // Positions
                $positions = [1=>'Chair',2=>'Clerk',3=>'LA',4=>'Adviser'];

                $output .= '<div class="result-body" id="sacre_' . $key . '">';
                    if (!empty($sacre['contacts'])) {
                        $output .= '<div class="contacts">';
                        $output .= '<h3 class="">Contacts</h3>';
                        $output .= '<ul class="">';
                        foreach ($sacre['contacts'] as $ckey => $contact) {
                            $position_title = $contact['position']['title'];
                            $hide_email = in_array(strtolower($position_title), ['chair', 'advisor', 'adviser'], true);

                            $output .= '<li class="flex justify-between border-b border-blue-900 py-2">';
                            $output .= '<p><strong>'.esc_html($position_title).'</strong> '.esc_html($contact['name']).'</p>';
                            if (!$hide_email) {
                                $output .= '<p><a class="cursor-pointer hover:text-orange-900" href="mailto:'.esc_attr($contact['email']).'">'.esc_html($contact['email']).'</a></p>';
                            }
                            $output .= '</li>';
                            if (array_key_exists($contact['position']['id'], $positions)) {
                                unset($positions[$contact['position']['id']]);
                            }
                        }
                        foreach ($positions as $pos) {
                            $output .= '<li class="flex justify-between border-b border-blue-900 py-2"><p><strong>'.esc_html($pos).'</strong></p></li>';
                        }
                        $output .= '</ul>';
                        $output .= '</div>';
                    }

                    if (!empty($sacre['files'])) {
                        $output .= '<div class="files">';
                        $output .= '<h3 class="text-2xl mb-4">Reports</h3><ul class="border-t border-blue-900">';
                        foreach ($sacre['files'] as $file) {
                            $output .= '<li class="flex justify-between border-b border-blue-900 py-2">';
                            $output .= '<a target="_blank" class="block w-full cursor-pointer hover:text-orange-900" href="https://database.nasacre.org.uk/api/database/'.esc_attr($sacre['id']).'/files/'.esc_attr($file['id']).'">View '.esc_html($file['year']).' Report</a></li>';
                        }
                        $output .= '</ul>';
                        $output .= '</div>';
                    }

                $output .= '</div>'; // sacre div

            $output .= '</div>'; // border div
        }

        $output .= '</div>'; // close wrappers
    }

    return $output;
}
add_shortcode('database_items', 'nasacre_database_shortcode');

// Enqueue styles
function nasacre_database_enqueue_styles() {
    wp_enqueue_style(
        'nasacre-database-style',
        plugin_dir_url(__FILE__) . 'assets/css/database.css',
        [],
        '1.2.4'
    );
}
add_action('wp_enqueue_scripts', 'nasacre_database_enqueue_styles');

function nasacre_database_enqueue_scripts() {
    wp_enqueue_script(
        'nasacre-database-script',
        plugin_dir_url(__FILE__) . 'assets/js/database.js',
        [],
        '1.0.0',
        true // load in footer
    );
}
add_action('wp_enqueue_scripts', 'nasacre_database_enqueue_scripts');
