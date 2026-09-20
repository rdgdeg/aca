<?php

namespace App\Services;

use App\Models\Merchant;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class OpeningStatus
{
    public function for(Merchant $merchant, ?CarbonInterface $at = null): array
    {
        $at = Carbon::instance(($at ?? now('Europe/Brussels'))->copy()->timezone('Europe/Brussels'));

        $closure = $merchant->closures
            ->first(fn ($c) => $at->toDateString() >= $c->starts_on->toDateString()
                && $at->toDateString() <= $c->ends_on->toDateString());

        if ($closure) {
            return [
                'key' => 'exceptional',
                'label' => __('Fermé exceptionnellement'),
                'detail' => $closure->t('message') ?: __('Fermé pour congés jusqu’au :date', ['date' => $closure->ends_on->translatedFormat('d/m')]),
                'open' => false,
            ];
        }

        if ($merchant->holiday_closed && $this->isBelgianHoliday($at)) {
            return [
                'key' => 'holiday',
                'label' => __('Fermé'),
                'detail' => __('Fermé les jours fériés'),
                'open' => false,
            ];
        }

        $ranges = $this->rangesFor($merchant, $at);

        foreach ($ranges as $range) {
            [$open, $close] = $range;
            if ($at->betweenIncluded($open, $close)) {
                $minutesLeft = $at->diffInMinutes($close, false);
                if ($minutesLeft <= 30) {
                    return [
                        'key' => 'closing_soon',
                        'label' => __('Ferme bientôt'),
                        'detail' => __('Ferme à :time', ['time' => $close->format('H:i')]),
                        'open' => true,
                    ];
                }

                return [
                    'key' => 'open',
                    'label' => __('Ouvert'),
                    'detail' => __('Jusqu’à :time', ['time' => $close->format('H:i')]),
                    'open' => true,
                ];
            }
        }

        $next = $this->nextOpening($merchant, $at);
        if ($next) {
            $sameDay = $next->isSameDay($at);

            return [
                'key' => 'closed',
                'label' => __('Fermé'),
                'detail' => $sameDay
                    ? __('Ouvre à :time', ['time' => $next->format('H:i')])
                    : __('Ouvre :day à :time', ['day' => $next->translatedFormat('l'), 'time' => $next->format('H:i')]),
                'open' => false,
            ];
        }

        return [
            'key' => 'closed',
            'label' => __('Fermé'),
            'detail' => '',
            'open' => false,
        ];
    }

    /**
     * @return array<int, array{0: Carbon, 1: Carbon}>
     */
    public function rangesFor(Merchant $merchant, CarbonInterface $day): array
    {
        $weekday = (int) $day->isoWeekday();
        $hours = $merchant->openingHours->where('weekday', $weekday);
        $ranges = [];

        foreach ($hours as $slot) {
            $open = Carbon::parse($day->toDateString().' '.$slot->opens_at, 'Europe/Brussels');
            $close = Carbon::parse($day->toDateString().' '.$slot->closes_at, 'Europe/Brussels');
            if ($close->lessThanOrEqualTo($open)) {
                $close->addDay();
            }
            $ranges[] = [$open, $close];
        }

        $yesterday = $day->copy()->subDay();
        $yHours = $merchant->openingHours->where('weekday', (int) $yesterday->isoWeekday());
        foreach ($yHours as $slot) {
            $open = Carbon::parse($yesterday->toDateString().' '.$slot->opens_at, 'Europe/Brussels');
            $close = Carbon::parse($yesterday->toDateString().' '.$slot->closes_at, 'Europe/Brussels');
            if ($close->lessThanOrEqualTo($open)) {
                $close->addDay();
                if ($day->betweenIncluded($open, $close)) {
                    $ranges[] = [$open, $close];
                }
            }
        }

        return $ranges;
    }

    public function isOpenOnSunday(Merchant $merchant): bool
    {
        return $merchant->open_sundays || $merchant->openingHours->contains(fn ($h) => (int) $h->weekday === 7);
    }

    public function nextOpening(Merchant $merchant, CarbonInterface $from): ?Carbon
    {
        for ($i = 0; $i < 8; $i++) {
            $day = $from->copy()->addDays($i)->timezone('Europe/Brussels');
            foreach ($this->rangesFor($merchant, $day) as [$open, $close]) {
                if ($open->greaterThan($from)) {
                    return $open;
                }
            }
        }

        return null;
    }

    public function isBelgianHoliday(CarbonInterface $date): bool
    {
        $d = $date->copy()->timezone('Europe/Brussels');
        $year = $d->year;
        $fixed = [
            sprintf('%d-01-01', $year),
            sprintf('%d-05-01', $year),
            sprintf('%d-07-21', $year),
            sprintf('%d-08-15', $year),
            sprintf('%d-11-01', $year),
            sprintf('%d-11-11', $year),
            sprintf('%d-12-25', $year),
        ];

        $easter = Carbon::createFromTimestamp(easter_date($year), 'Europe/Brussels')->startOfDay();
        $moveable = [
            $easter->copy()->addDay()->toDateString(),
            $easter->copy()->addDays(39)->toDateString(),
            $easter->copy()->addDays(50)->toDateString(),
        ];

        return in_array($d->toDateString(), array_merge($fixed, $moveable), true);
    }
}
