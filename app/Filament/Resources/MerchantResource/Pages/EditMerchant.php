<?php

namespace App\Filament\Resources\MerchantResource\Pages;

use App\Filament\Concerns\TranslatesFromFrench;
use App\Filament\Resources\Concerns\FillsTranslations;
use App\Filament\Resources\MerchantResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMerchant extends EditRecord
{
    use FillsTranslations;
    use TranslatesFromFrench;

    protected static string $resource = MerchantResource::class;

    protected function getHeaderActions(): array
    {
        return [$this->translateFromFrenchAction(), DeleteAction::make()->label('Supprimer')];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = MerchantResource::fillSlug($data, $this->record);

        return MerchantResource::preserveExistingCover($data, $this->record->cover_url);
    }

    protected function afterSave(): void
    {
        MerchantResource::geocodeIfNeeded($this->record);
    }
}
