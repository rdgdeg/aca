<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeSuggestion extends Model
{
    protected $fillable = [
        'merchant_id', 'sender_name', 'sender_email', 'sender_role',
        'payload', 'original', 'status', 'reviewed_by', 'reviewed_at', 'admin_comment',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'original' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
