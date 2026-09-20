<?php

namespace App\Filament\Widgets;

use App\Enums\MembershipStatus;
use App\Enums\PublishStatus;
use App\Enums\SubmissionStatus;
use App\Models\ChangeSuggestion;
use App\Models\Deal;
use App\Models\Event;
use App\Models\MembershipApplication;
use App\Models\Merchant;
use App\Models\Submission;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AcaStatsWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'À traiter';

    protected int|array|null $columns = 3;

    protected function getStats(): array
    {
        return [
            Stat::make('Messages non lus', (string) Submission::query()->where('status', SubmissionStatus::New)->count())
                ->description('Messagerie publique'),
            Stat::make('Suggestions', (string) ChangeSuggestion::query()->where('status', 'pending')->count())
                ->description('Modifications proposées'),
            Stat::make('Événements à valider', (string) Event::query()->where('status', PublishStatus::Pending)->count()),
            Stat::make('Bons plans à valider', (string) Deal::query()->where('status', PublishStatus::Pending)->count()),
            Stat::make('Adhésions', (string) MembershipApplication::query()->where('status', MembershipStatus::New)->count()),
            Stat::make('Fiches sans horaires', (string) Merchant::query()->doesntHave('openingHours')->count()),
        ];
    }
}
