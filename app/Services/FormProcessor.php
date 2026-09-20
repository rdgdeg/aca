<?php

namespace App\Services;

use App\Enums\MembershipStatus;
use App\Enums\PublishStatus;
use App\Enums\SubmissionStatus;
use App\Mail\SubmissionAckMail;
use App\Mail\SubmissionAdminMail;
use App\Models\Event;
use App\Models\Form;
use App\Models\MembershipApplication;
use App\Models\Setting;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FormProcessor
{
    public function rules(Form $form): array
    {
        $rules = [
            'website' => ['nullable', 'max:0'],
            'consent' => ['accepted'],
        ];

        foreach ($form->fields as $field) {
            $key = 'fields.'.$field->id;
            $fieldRules = [];
            if ($field->required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            $fieldRules = array_merge($fieldRules, match ($field->type) {
                'email' => ['email'],
                'number' => ['numeric'],
                'date' => ['date'],
                'checkbox' => ['array'],
                'file' => ['file', 'max:'.(($field->max_size ?: 10) * 1024)],
                default => ['string', 'max:5000'],
            });
            $rules[$key] = $fieldRules;
        }

        return $rules;
    }

    public function submit(Form $form, Request $request, ?Event $event = null): Submission
    {
        $validated = $request->validate($this->rules($form));

        if ($form->system_key === 'join') {
            $this->assertJoinHasCategory($form, $validated['fields'] ?? []);
        }

        if ($event && $event->capacity && $event->remainingPlaces() !== null && $event->remainingPlaces() <= 0 && ! $event->waitlist) {
            throw ValidationException::withMessages(['form' => __('Cet événement est complet.')]);
        }

        $data = [];
        foreach ($form->fields as $field) {
            if ($field->type === 'heading' || $field->type === 'help') {
                continue;
            }
            $data[$field->t('label')] = $validated['fields'][$field->id] ?? null;
        }

        $submission = Submission::query()->create([
            'form_id' => $form->id,
            'event_id' => $event?->id,
            'locale' => aca_locale(),
            'data' => $data,
            'status' => SubmissionStatus::New,
            'ip_hash' => $request->ip() ? hash('sha256', $request->ip()) : null,
            'status_history' => [['status' => 'new', 'at' => now()->toIso8601String()]],
        ]);

        if ($form->system_key === 'join') {
            MembershipApplication::query()->create([
                'submission_id' => $submission->id,
                'company_name' => $this->firstValue($data, ['raison sociale', 'entreprise', 'company', 'nom']) ?? 'Demande',
                'vat_number' => $this->firstValue($data, ['tva', 'bce', 'vat', 'entreprise']),
                'status' => MembershipStatus::New,
                'payload' => $data,
            ]);
        }

        if ($form->system_key === 'event') {
            Event::query()->create([
                'title' => ['fr' => $this->firstValue($data, ['titre', 'title']) ?? 'Proposition'],
                'slug' => ['fr' => Str::slug($this->firstValue($data, ['titre', 'title']) ?? 'proposition-'.uniqid())],
                'description' => ['fr' => $this->firstValue($data, ['description']) ?? ''],
                'location' => $this->firstValue($data, ['lieu', 'location']),
                'status' => PublishStatus::Pending,
                'proposed_by_submission_id' => $submission->id,
                'starts_at' => now()->addWeek(),
            ]);
        }

        $admin = Setting::string('admin_email', config('mail.from.address'));
        if ($admin) {
            Mail::to($admin)->queue(new SubmissionAdminMail($submission));
        }
        if ($form->ack_enabled && $email = $submission->senderEmail()) {
            Mail::to($email)->queue(new SubmissionAckMail($submission));
        }

        return $submission;
    }

    private function firstValue(array $data, array $needles): ?string
    {
        foreach ($data as $label => $value) {
            foreach ($needles as $needle) {
                if (str_contains(mb_strtolower((string) $label), $needle) && is_scalar($value) && $value !== '') {
                    return (string) $value;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<int|string, mixed>  $fields
     */
    private function assertJoinHasCategory(Form $form, array $fields): void
    {
        $categoryField = $form->fields->first(function ($field) {
            return $field->type === 'checkbox' && str_contains(mb_strtolower($field->t('label', 'fr')), 'catégorie');
        });
        $otherField = $form->fields->first(function ($field) {
            return $field->type !== 'checkbox' && str_contains(mb_strtolower($field->t('label', 'fr')), 'autre catégorie');
        });

        $selected = $categoryField
            ? array_filter((array) ($fields[$categoryField->id] ?? []))
            : [];
        $other = $otherField ? trim((string) ($fields[$otherField->id] ?? '')) : '';

        if ($selected === [] && $other === '') {
            throw ValidationException::withMessages([
                'fields.'.($categoryField?->id ?? 'category') => __('Cochez une catégorie ou indiquez-en une autre.'),
            ]);
        }
    }
}
