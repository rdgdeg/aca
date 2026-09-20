<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasFrenchLabels;
use App\Filament\Resources\ChangeSuggestionResource\Pages;
use App\Models\ChangeSuggestion;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ChangeSuggestionResource extends Resource
{
    use HasFrenchLabels;

    protected static ?string $model = ChangeSuggestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string|\UnitEnum|null $navigationGroup = 'Annuaire';

    protected static ?string $modelLabel = 'Suggestion';

    protected static ?string $pluralModelLabel = 'Suggestions';

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('merchant.name')->label('Commerçant'),
            TextColumn::make('sender_name')->label('Expéditeur'),
            TextColumn::make('sender_role')->label('Rôle'),
            TextColumn::make('status')->label('Statut')->badge(),
            TextColumn::make('created_at')->label('Reçue')->since(),
        ])->filters([
            SelectFilter::make('status')->label('Statut')->options(['pending' => 'En attente', 'accepted' => 'Acceptée', 'rejected' => 'Refusée']),
        ])->recordActions([ViewAction::make()->label('Examiner')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChangeSuggestions::route('/'),
            'view' => Pages\ReviewChangeSuggestion::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = ChangeSuggestion::query()->where('status', 'pending')->count();

        return $count ? (string) $count : null;
    }
}
