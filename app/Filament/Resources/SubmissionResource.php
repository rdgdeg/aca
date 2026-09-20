<?php

namespace App\Filament\Resources;

use App\Enums\SubmissionStatus;
use App\Filament\Concerns\HasFrenchLabels;
use App\Filament\Resources\SubmissionResource\Pages;
use App\Models\Submission;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubmissionResource extends Resource
{
    use HasFrenchLabels;

    protected static ?string $model = Submission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|\UnitEnum|null $navigationGroup = 'Messages';

    protected static ?string $modelLabel = 'Demande';

    protected static ?string $pluralModelLabel = 'Demandes';

    protected static ?string $navigationLabel = 'Demandes';

    protected static ?int $navigationSort = 2;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('form.name')->label('Formulaire'),
            TextEntry::make('created_at')->label('Reçue le')->dateTime('d/m/Y H:i'),
            TextEntry::make('locale')->label('Langue'),
            TextEntry::make('status')->label('Statut')->badge(),
            TextEntry::make('data')->label('Message')->formatStateUsing(function ($state) {
                if (! is_array($state)) {
                    return $state;
                }

                return collect($state)->map(fn ($v, $k) => $k.': '.(is_array($v) ? implode(', ', $v) : $v))->implode("\n");
            })->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('form.name')->label('Formulaire'),
                TextColumn::make('created_at')->label('Reçue')->since(),
                TextColumn::make('status')->label('Statut')->badge(),
                TextColumn::make('locale')->label('Langue'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(collect(SubmissionStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
                SelectFilter::make('form_id')->label('Formulaire')->relationship('form', 'name'),
            ])
            ->recordActions([
                ViewAction::make()->label('Voir'),
                Action::make('treat')->label('Marquer traité')->action(fn (Submission $r) => $r->update(['status' => SubmissionStatus::Treated])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubmissions::route('/'),
            'view' => Pages\ViewSubmission::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Submission::query()->where('status', SubmissionStatus::New)->count();

        return $count ? (string) $count : null;
    }
}
