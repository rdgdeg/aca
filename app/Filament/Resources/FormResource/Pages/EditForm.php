<?php

namespace App\Filament\Resources\FormResource\Pages;

use App\Filament\Resources\Concerns\FillsTranslations;
use App\Filament\Resources\FormResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditForm extends EditRecord
{
    use FillsTranslations;

    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->label('Supprimer')];
    }
}
