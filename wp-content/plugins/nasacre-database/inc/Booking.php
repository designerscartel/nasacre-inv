<?php

namespace inc;

class Booking
{
    public function createBooking($booking = null)
    {
        if (! is_array($booking)) {
            return new \WP_Error('invalid_booking', 'Booking data is required.');
        }

        $required = ['id', 'sacre_id', 'booking_name', 'booking_email', 'booking_phone', 'delegate', 'confirmed'];
        foreach ($required as $key) {
            if (! array_key_exists($key, $booking)) {
                return new \WP_Error('invalid_booking', "Missing booking field: {$key}.");
            }
        }

        $body = [
            'id' => absint($booking['id']),
            'sacre_id' => absint($booking['sacre_id']),
            'name' => sanitize_text_field($booking['booking_name']),
            'email' => sanitize_email($booking['booking_email']),
            'phone' => sanitize_text_field($booking['booking_phone']),
            'delegate' => absint($booking['delegate']),
            'offer' => isset($booking['offer']) ? absint($booking['offer']) : 0,
            'confirmed' => rest_sanitize_boolean($booking['confirmed']),
        ];

        if (! is_email($body['email'])) {
            return new \WP_Error('invalid_booking_email', 'A valid booking email is required.');
        }

        $url = apply_filters('nasacre_booking_api_url', 'https://database.nasacre.org.uk/api/booking');
        $response = wp_remote_post($url, [
            'timeout' => 15,
            'sslverify' => true,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode($body),
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $status = wp_remote_retrieve_response_code($response);
        if ($status < 200 || $status >= 300) {
            return new \WP_Error('booking_api_error', "Booking API returned HTTP {$status}.");
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (! is_array($data)) {
            return new \WP_Error('booking_api_error', 'Booking API returned an invalid response.');
        }

        return $data;
    }
}
