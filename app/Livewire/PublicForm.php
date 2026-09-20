<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Form;
use App\Services\FormProcessor;
use Illuminate\Http\Request;
use Livewire\Component;

class PublicForm extends Component
{
    public Form $form;

    public ?int $eventId = null;

    public array $fields = [];

    public bool $consent = false;

    public string $website = '';

    public bool $sent = false;

    public string $success = '';

    public function mount(Form $form, ?int $eventId = null): void
    {
        $this->form = $form->load('fields');
        $this->eventId = $eventId;
        foreach ($this->form->fields as $field) {
            $this->fields[$field->id] = $field->type === 'checkbox' ? [] : '';
        }
    }

    public function submit(FormProcessor $processor, Request $request)
    {
        if ($this->website !== '') {
            return;
        }

        $request->merge([
            'fields' => $this->fields,
            'consent' => $this->consent ? 'on' : null,
            'website' => $this->website,
            'event_id' => $this->eventId,
        ]);

        $event = $this->eventId ? Event::query()->find($this->eventId) : null;
        $processor->submit($this->form, $request, $event);
        $this->sent = true;
        $this->success = $this->form->t('success_message') ?: __('Merci, votre message a bien été envoyé.');
    }

    public function render()
    {
        return view('livewire.public-form');
    }
}
