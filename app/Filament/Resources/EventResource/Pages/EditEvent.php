<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Concerns\TranslatesFromFrench;
use App\Filament\Resources\Concerns\FillsTranslations;
use App\Filament\Resources\EventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    use FillsTranslations;
    use TranslatesFromFrench;

    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [$this->translateFromFrenchAction(), DeleteAction::make()->label('Supprimer')];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = EventResource::fillSlug($data, $this->record);

        return EventResource::preserveExistingCover($data, $this->record->cover_url);
    }
}
