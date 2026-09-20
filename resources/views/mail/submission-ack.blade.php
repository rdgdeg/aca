@component('mail::message')
{!! nl2br(e($submission->form->t('ack_body') ?: 'Nous avons bien reçu votre message et vous répondrons dans les meilleurs délais.')) !!}

@foreach ($submission->data as $label => $value)
**{{ $label }}**  
{{ is_array($value) ? implode(', ', $value) : $value }}

@endforeach

{{ config('app.name') }}
@endcomponent
