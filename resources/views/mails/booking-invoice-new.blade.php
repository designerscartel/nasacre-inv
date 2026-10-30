@php
    $date = \Carbon\Carbon::parse($sacreBooking->booking->date);

    $total = $sacreBooking->booking->priceForDelegateCount($delegates->count(), (bool) $sacreBooking->sacre->member) ?? 0;

    if($sacreBooking->offer != 0) {
        $total = $total + $sacreBooking->booking->additional_amount;
    }

@endphp

@component('mail::message')

<p>For:<br/>
<strong>Attendance at the NASACRE Conference and AGM {{ $date->format('Y') }}</strong><br/>
{{ $sacreBooking->booking->venue }}
<br/>SACRE: {{ $sacreBooking->sacre->title }}</p>

@if($sacreBooking->offer != 0)
Bonus offer for Subscribed SACREs £{{ $sacreBooking->booking->additional_amount }}
@endif

@foreach($delegates as $key => $delegate)
<p>{{ $delegate->name }}<br />
{{ $sacreBooking->sacre->title }}</p>
@endforeach

Total: £{{ $total }}

@endcomponent
