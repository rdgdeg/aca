<?php

namespace App\Models;

use App\Support\TranslatableFallback;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class FormField extends Model
{
    use HasTranslations, TranslatableFallback;

    public array $translatable = ['label', 'help', 'options'];

    protected $fillable = [
        'form_id', 'type', 'label', 'help', 'name', 'options', 'required',
        'width', 'accept', 'max_size', 'max_files', 'position',
    ];

    protected function casts(): array
    {
        return [
            'required' => 'boolean',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function optionList(?string $locale = null): array
    {
        $raw = $this->t('options', $locale);
        if ($raw === '') {
            $raw = $this->t('options', 'fr');
        }

        return array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $raw) ?: [])));
    }
}
