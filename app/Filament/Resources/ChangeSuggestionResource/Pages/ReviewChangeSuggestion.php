<?php

namespace App\Filament\Resources\ChangeSuggestionResource\Pages;

use App\Filament\Resources\ChangeSuggestionResource;
use App\Services\SuggestionApplier;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ReviewChangeSuggestion extends ViewRecord
{
    protected static string $resource = ChangeSuggestionResource::class;

    protected string $view = 'filament.pages.review-suggestion';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('accept')
                ->label('Accepter les champs')
                ->form([
                    CheckboxList::make('fields')
                        ->options(fn () => collect($this->record->payload ?? [])->keys()->mapWithKeys(fn ($k) => [$k => $k])->all())
                        ->default(array_keys($this->record->payload ?? []))
                        ->required(),
                ])
                ->action(function (array $data, SuggestionApplier $applier) {
                    $applier->apply($this->record, $data['fields'], auth()->user());
                    Notification::make()->title('Fiche mise à jour')->success()->send();
                    $this->redirect(ChangeSuggestionResource::getUrl());
                })
                ->visible(fn () => $this->record->status === 'pending'),
            Action::make('reject')
                ->label('Refuser')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (SuggestionApplier $applier) {
                    $applier->reject($this->record, auth()->user());
                    Notification::make()->title('Suggestion refusée')->send();
                    $this->redirect(ChangeSuggestionResource::getUrl());
                })
                ->visible(fn () => $this->record->status === 'pending'),
        ];
    }
}
