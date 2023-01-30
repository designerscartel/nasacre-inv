@php
    $total = 0;
    $delegateCost = ($sacreBooking->sacre->member)? $sacreBooking->booking->subscribed : $sacreBooking->booking->none_subscribed;

    if(!empty($sacreBooking->delegate_one_name)) {
        $total = $total + $delegateCost;
    }
    if(!empty($sacreBooking->delegate_two_name)) {
        $total = $total + $delegateCost;
    }

    if(!empty($sacreBooking->virtual_one_name)) {
        $total = $total + $sacreBooking->booking->additional;
    }
@endphp

@component('mail::message')

    {{ $sacreBooking->name }}
    {{ $sacreBooking->email }}
    {{ $sacreBooking->phone }}

    {{ $sacreBooking->delegate_one_name }}
    {{ $sacreBooking->delegate_one_email }}
    {{ $sacreBooking->delegate_one_diet }}
    {{ $sacreBooking->delegate_two_name }}
    {{ $sacreBooking->delegate_two_email }}
    {{ $sacreBooking->delegate_two_diet }}
    {{ $sacreBooking->virtual_one_name }}
    {{ $sacreBooking->virtual_one_email }}
    {{ $sacreBooking->virtual_two_name }}
    {{ $sacreBooking->virtual_two_email }}

    {{ $total }}


@endcomponent
