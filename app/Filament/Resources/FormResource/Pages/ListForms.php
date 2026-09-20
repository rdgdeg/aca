<?php

namespace App\Filament\Resources\FormResource\Pages;

use App\Filament\Resources\FormResource;
use App\Filament\Resources\SubmissionResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListForms extends ListRecords
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('allSubmissions')
                ->label('Toutes les demandes')
                ->url(SubmissionResource::getUrl('index')),
            CreateAction::make()->label('Nouveau formulaire'),
        ];
    }
}
