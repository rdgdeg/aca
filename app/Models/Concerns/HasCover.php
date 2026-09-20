<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

trait HasCover
{
    public function cover(): ?string
    {
        if (method_exists($this, 'hasMedia') && $this->hasMedia('cover')) {
            return $this->getFirstMediaUrl('cover');
        }

        $url = $this->cover_url ?? null;
        if (! $url) {
            return asset('images/logo-aca.jpg');
        }
        if (str_starts_with($url, 'http') || str_starts_with($url, '/')) {
            return $url;
        }

        return Storage::disk('public')->url($url);
    }
}
