<?php

namespace App\Models;

use App\Support\TranslatableFallback;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ClosurePeriod extends Model
{
    use HasTranslations, TranslatableFallback;

    protected $table = 'closures';

    public array $translatable = ['message'];

    protected $fillable = ['merchant_id', 'starts_on', 'ends_on', 'message'];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
