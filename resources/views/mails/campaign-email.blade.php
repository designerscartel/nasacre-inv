@component('mail::message')
# Hello {{ $email->name }}

{{ $campaign->subject }}

{{ $campaign->message }}

@endcomponent
