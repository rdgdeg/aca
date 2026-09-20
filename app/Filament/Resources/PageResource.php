<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasFrenchLabels;
use App\Filament\Concerns\HasLocalizedFields;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageResource extends Resource
{
    use HasFrenchLabels;
    use HasLocalizedFields;

    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Site';

    protected static ?string $modelLabel = 'Page';

    protected static ?string $pluralModelLabel = 'Pages';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            self::localizedTabs('title', 'Titre', false, true),
            self::localizedTabs('slug', 'Slug', false, true),
            Repeater::make('blocks.fr')->label('Blocs FR')->schema([
                Select::make('type')->label('Type')->options(['text' => 'Texte', 'cta' => 'Appel à l’action', 'form' => 'Formulaire'])->required(),
                Textarea::make('body')->label('Texte')->rows(4),
                TextInput::make('label')->label('Libellé'),
                TextInput::make('url')->label('Lien'),
                TextInput::make('form_key')->label('Clé formulaire'),
            ]),
            Toggle::make('is_published')->label('Publiée')->default(true),
            self::localizedTabs('seo_title', 'SEO titre'),
            self::localizedTabs('seo_description', 'SEO description', true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Titre')->formatStateUsing(fn ($r) => $r->t('title', 'fr')),
            IconColumn::make('is_published')->boolean()->label('Publiée'),
        ])->recordActions([
            EditAction::make()->label('Modifier'),
            DeleteAction::make()->label('Supprimer'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePages::route('/'),
        ];
    }
}
