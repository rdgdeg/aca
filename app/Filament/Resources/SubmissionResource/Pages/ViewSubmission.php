<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Enums\SubmissionStatus;
use App\Filament\Resources\SubmissionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('read')->label('Marquer lu')->action(fn () => $this->record->update(['status' => SubmissionStatus::Read])),
            Action::make('treated')->label('Traité')->action(fn () => $this->record->update(['status' => SubmissionStatus::Treated])),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);
        if ($this->record->status === SubmissionStatus::New) {
            $this->record->update(['status' => SubmissionStatus::Read]);
        }
    }
}
