<?php

namespace App\Filament\Resources\FormResource\RelationManagers;

use App\Filament\Resources\SubmissionResource;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'Demandes reçues';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->dateTime('d/m/Y H:i')->label('Date'),
                TextColumn::make('status')->badge()->label('Statut'),
                TextColumn::make('locale')->label('Langue'),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Voir')
                    ->url(fn (Submission $record): string => SubmissionResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateHeading('Aucune demande pour l’instant');
    }
}
