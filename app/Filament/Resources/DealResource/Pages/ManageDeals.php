<?php

namespace App\Filament\Resources\DealResource\Pages;

use App\Filament\Resources\Concerns\FillsTranslations;
use App\Filament\Resources\DealResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDeals extends ManageRecords
{
    use FillsTranslations;

    protected static string $resource = DealResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nouveau bon plan')];
    }
}
