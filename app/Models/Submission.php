<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Submission extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'form_id', 'event_id', 'locale', 'data', 'status',
        'internal_notes', 'ip_hash', 'status_history',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'status' => SubmissionStatus::class,
            'status_history' => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function senderEmail(): ?string
    {
        $data = $this->data ?? [];

        foreach (['email', 'e-mail', 'mail'] as $key) {
            foreach ($data as $k => $v) {
                if (str_contains(strtolower((string) $k), $key) && is_string($v) && filter_var($v, FILTER_VALIDATE_EMAIL)) {
                    return $v;
                }
            }
        }

        return null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }
}
