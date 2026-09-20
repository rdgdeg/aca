@component('mail::message')
# Nouveau message — {{ $submission->form->name }}

@foreach ($submission->data as $label => $value)
**{{ $label }}**  
{{ is_array($value) ? implode(', ', $value) : $value }}

@endforeach

[Ouvrir dans l’admin]({{ url('/admin/submissions/'.$submission->id) }})
@endcomponent
