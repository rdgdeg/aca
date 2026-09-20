<?php

namespace App\Services;

use App\Models\ChangeSuggestion;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Arr;

class SuggestionApplier
{
    public function diff(Merchant $merchant, array $incoming): array
    {
        $watched = ['name', 'description', 'short_text', 'address', 'phone', 'email', 'website', 'highlights', 'brands'];
        $original = [];
        $changed = [];

        foreach ($watched as $field) {
            $current = $merchant->{$field};
            if (array_key_exists($field, $incoming) && $this->normalize($incoming[$field]) !== $this->normalize($current)) {
                $original[$field] = $current;
                $changed[$field] = $incoming[$field];
            }
        }

        if (isset($incoming['hours']) && is_array($incoming['hours'])) {
            $currentHours = $merchant->openingHours->map(fn ($h) => [
                'weekday' => (int) $h->weekday,
                'opens_at' => substr((string) $h->opens_at, 0, 5),
                'closes_at' => substr((string) $h->closes_at, 0, 5),
            ])->values()->all();
            if ($this->normalize($incoming['hours']) !== $this->normalize($currentHours)) {
                $original['hours'] = $currentHours;
                $changed['hours'] = $incoming['hours'];
            }
        }

        if (isset($incoming['social']) && is_array($incoming['social'])) {
            $currentSocial = $merchant->socialLinks->mapWithKeys(fn ($l) => [$l->network => $l->url])->all();
            if ($this->normalize($incoming['social']) !== $this->normalize($currentSocial)) {
                $original['social'] = $currentSocial;
                $changed['social'] = $incoming['social'];
            }
        }

        return [$original, $changed];
    }

    public function apply(ChangeSuggestion $suggestion, array $acceptedFields, User $reviewer): Merchant
    {
        $merchant = $suggestion->merchant;
        $payload = $suggestion->payload ?? [];

        foreach ($acceptedFields as $field) {
            if (! array_key_exists($field, $payload)) {
                continue;
            }
            if ($field === 'hours') {
                $merchant->openingHours()->delete();
                foreach ($payload['hours'] as $i => $slot) {
                    if (empty($slot['opens_at']) || empty($slot['closes_at'])) {
                        continue;
                    }
                    $merchant->openingHours()->create([
                        'weekday' => (int) $slot['weekday'],
                        'opens_at' => $slot['opens_at'],
                        'closes_at' => $slot['closes_at'],
                        'position' => $i,
                    ]);
                }

                continue;
            }
            if ($field === 'social') {
                $merchant->socialLinks()->delete();
                foreach ($payload['social'] as $network => $url) {
                    if ($url) {
                        $merchant->socialLinks()->create(['network' => $network, 'url' => $url]);
                    }
                }

                continue;
            }
            $merchant->{$field} = $payload[$field];
        }

        $merchant->save();

        $suggestion->update([
            'status' => 'accepted',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        return $merchant->fresh();
    }

    public function reject(ChangeSuggestion $suggestion, User $reviewer, ?string $comment = null): void
    {
        $suggestion->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'admin_comment' => $comment,
        ]);
    }

    private function normalize(mixed $value): string
    {
        if (is_array($value)) {
            $value = Arr::sortRecursive($value);
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    }
}
