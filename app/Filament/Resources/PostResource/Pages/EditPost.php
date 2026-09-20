<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Concerns\TranslatesFromFrench;
use App\Filament\Resources\Concerns\FillsTranslations;
use App\Filament\Resources\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    use FillsTranslations;
    use TranslatesFromFrench;

    protected static string $resource = PostResource::class;

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
        return PostResource::preserveExistingCover($data, $this->record->cover_url);
    }
}
