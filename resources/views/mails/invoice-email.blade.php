@component('mail::message')
# Hello {{ $contact->name }}

{{ $sacre->title }}

{{ $invoice->year }}

{{ $invoice->message }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
