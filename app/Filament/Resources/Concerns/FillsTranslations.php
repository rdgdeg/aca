<?php

namespace App\Filament\Resources\Concerns;

trait FillsTranslations
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        if ($record && property_exists($record, 'translatable')) {
            foreach ($record->translatable as $attribute) {
                $data[$attribute] = $record->getTranslations($attribute);
            }
        }

        return $data;
    }
}
