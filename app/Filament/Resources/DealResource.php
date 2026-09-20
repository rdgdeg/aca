<?php

namespace App\Filament\Resources;

use App\Enums\PublishStatus;
use App\Filament\Concerns\HasCoverUpload;
use App\Filament\Concerns\HasFrenchLabels;
use App\Filament\Concerns\HasLocalizedFields;
use App\Filament\Resources\DealResource\Pages;
use App\Models\Deal;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DealResource extends Resource
{
    use HasCoverUpload;
    use HasFrenchLabels;
    use HasLocalizedFields;

    protected static ?string $model = Deal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|\UnitEnum|null $navigationGroup = 'Centre-ville';

    protected static ?string $modelLabel = 'Bon plan';

    protected static ?string $pluralModelLabel = 'Bons plans';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Offre')
                ->description('Le français est obligatoire. Les traductions NL/EN peuvent rester vides.')
                ->schema([
                    Select::make('merchant_id')->label('Commerçant')->relationship('merchant', 'name')->required()->searchable()->preload(),
                    self::localizedTabs('title', 'Titre', false, true),
                    self::localizedTabs('description', 'Description', true),
                    self::localizedTabs('conditions', 'Conditions', true),
                    self::coverUpload('deals', 'Image'),
                ]),
            Section::make('Validité')
                ->schema([
                    DatePicker::make('starts_on')->label('Début')->native(false)->displayFormat('d/m/Y')->locale('fr')->required(),
                    DatePicker::make('ends_on')->label('Fin')->native(false)->displayFormat('d/m/Y')->locale('fr')->required(),
                    Select::make('status')->label('Statut')->options(collect(PublishStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()]))->required(),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Titre')->formatStateUsing(fn ($r) => $r->t('title', 'fr')),
            TextColumn::make('merchant.name')->label('Commerçant'),
            TextColumn::make('ends_on')->label('Fin')->date('d/m/Y'),
            TextColumn::make('status')->label('Statut')->badge(),
        ])->recordActions([
            EditAction::make()->label('Modifier'),
            DeleteAction::make()->label('Supprimer'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDeals::route('/'),
        ];
    }
}
