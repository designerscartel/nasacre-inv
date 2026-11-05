<?php

namespace inc;
class SharedDatabase
{

    protected $databseUrl = 'https://database.nasacre.org.uk/api/shared';


    function getSacres($term = null)
    {
        $termName = '';
        if ($term !== null) {
            $termName = '_' . htmlspecialchars(strtolower(trim($term)));
            $content = get_transient('sharedd_results' . $termName);
        } else {
            $content = get_transient('sharedd_results');
        }

        if (false === $content) {
            $response = wp_remote_request($this->databseUrl . '/' . $term, array(
                'ssl_verify' => false,
                'sslverify' => false
            ));

            is_wp_error($response) and printf(
                'There was an ERROR in your request.<br />Code: %s<br />Message: %s',
                $response->get_error_code(),
                $response->get_error_message()
            );

            $content = trim(wp_remote_retrieve_body($response));
            if (strstr(wp_remote_retrieve_header($response, 'content-type'), 'json')) {
                set_transient('sharedd_results' . $termName, $content, 12 * MINUTE_IN_SECONDS);
            }
        }

        $data = json_decode($content, true);
        usort($data['data'], function ($item1, $item2) {
            return $item1['title'] <=> $item2['title'];
        });
        return $data['data'];
    }

}