@php
    $date = \Carbon\Carbon::parse($sacreBooking->booking->date);

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

<p>For:<br/>
<strong>Attendance at the NASACRE Conference and AGM {{ $date->format('Y') }}</strong><br/>
{{ $sacreBooking->booking->venue }}
<br/>SACRE: {{ $sacreBooking->sacre->title }}</p>

@if(!empty($sacreBooking->delegate_one_name))
{{ $sacreBooking->delegate_one_name }}
{{ $sacreBooking->delegate_one_email }}
{{ $sacreBooking->delegate_one_diet }}
Delegate fee: £{{ $delegateCost }}
@endif

@if(!empty($sacreBooking->delegate_two_name))
{{ $sacreBooking->delegate_two_name }}
{{ $sacreBooking->delegate_two_email }}
{{ $sacreBooking->delegate_two_diet }}
Delegate fee: £{{ $delegateCost }}
@endif

@if(!empty($sacreBooking->virtual_one_name))
{{ $sacreBooking->virtual_one_name }}
{{ $sacreBooking->virtual_one_email }}
Virtual Delegate fee: £{{ $sacreBooking->booking->additional }}
@endif

@if(!empty($sacreBooking->virtual_two_name))
{{ $sacreBooking->virtual_two_name }}
{{ $sacreBooking->virtual_two_email }}
@endif

Total: £{{ $total }}

@endcomponent
