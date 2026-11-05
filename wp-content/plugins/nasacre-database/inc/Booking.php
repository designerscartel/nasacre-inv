<?php

namespace inc;
class Booking
{

    protected $databseUrl = 'https://database.nasacre.org.uk/api/booking';


    function createBooking($booking = null)
    {
        if (!empty($booking)) {

            $body = array(
                'id' => $booking['id'],
                'sacre_id' => $booking['sacre_id'],
                'name' => $booking['booking_name'],
                'email' => $booking['booking_email'],
                'phone' => $booking['booking_phone'],
                'delegate' => $booking['delegate'],
                'offer' => (isset($booking['offer']) ? $booking['offer'] : 0),
                /*
                'delegate_one_name' => $booking['delegate_one_name'],
                'delegate_one_email' => $booking['delegate_one_email'],
                'delegate_one_diet' => $booking['delegate_one_diet'],
                'delegate_two_name' => $booking['delegate_two_name'],
                'delegate_two_email' => $booking['delegate_two_email'],
                'delegate_two_diet' => $booking['delegate_two_diet'],
                'virtual_one_name' => $booking['virtual_one_name'],
                'virtual_one_email' => $booking['virtual_one_email'],
                'virtual_two_name' => $booking['virtual_two_name'],
                'virtual_two_email' => $booking['virtual_two_email'],
                */
                'confirmed' => $booking['confirmed'],
            );

            $args = array(
                'method' => 'POST',
                'timeout' => 45,
                'sslverify' => false,
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'body' => json_encode($body),
            );

            $request = wp_remote_post($this->databseUrl, $args);

            if (!empty($request)) {
                return json_decode($request['body']);
            }

            if (is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200) {
                error_log(print_r($request, true));
            }


        }
    }

}