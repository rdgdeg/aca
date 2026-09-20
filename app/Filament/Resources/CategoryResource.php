<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasFrenchLabels;
use App\Filament\Concerns\HasLocalizedFields;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    use HasFrenchLabels;
    use HasLocalizedFields;

    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'Annuaire';

    protected static ?string $modelLabel = 'Catégorie';

    protected static ?string $pluralModelLabel = 'Catégories';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            self::localizedTabs('name', 'Nom', false, true),
            self::localizedTabs('slug', 'Slug', false, true),
            ColorPicker::make('pin_color')->label('Couleur du pin')->default('#5B3A7A'),
            TextInput::make('icon')->label('Icône'),
            TextInput::make('position')->label('Ordre')->numeric()->default(0),
            TextInput::make('cover_url')->label('Image'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nom')->formatStateUsing(fn ($record) => $record->t('name', 'fr')),
            TextColumn::make('position')->label('Ordre')->sortable(),
        ])->recordActions([
            EditAction::make()->label('Modifier'),
            DeleteAction::make()->label('Supprimer'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCategories::route('/'),
        ];
    }
}
