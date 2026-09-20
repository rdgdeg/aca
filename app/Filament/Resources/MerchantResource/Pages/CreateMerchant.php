<?php

namespace App\Filament\Resources\MerchantResource\Pages;

use App\Filament\Resources\MerchantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMerchant extends CreateRecord
{
    protected static string $resource = MerchantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return MerchantResource::fillSlug($data);
    }

    protected function afterCreate(): void
    {
        MerchantResource::geocodeIfNeeded($this->record);
    }
}
