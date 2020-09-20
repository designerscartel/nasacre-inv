@component('mail::message')
# Hello {{ $email->name }}

{{ $campaign->subject }}

{{ $campaign->message }}

{{ $campaign->url }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
