<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasCoverUpload;
use App\Filament\Concerns\HasFrenchLabels;
use App\Filament\Concerns\HasLocalizedFields;
use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostResource extends Resource
{
    use HasCoverUpload;
    use HasFrenchLabels;
    use HasLocalizedFields;

    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|\UnitEnum|null $navigationGroup = 'Centre-ville';

    protected static ?string $modelLabel = 'Article';

    protected static ?string $pluralModelLabel = 'Actualités';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contenu')
                ->description('Le français est obligatoire. Les traductions NL/EN peuvent rester vides.')
                ->schema([
                    self::localizedTabs('title', 'Titre', false, true),
                    self::localizedTabs('excerpt', 'Chapô', true),
                    self::localizedTabs('body', 'Corps', true),
                    self::coverUpload('posts', 'Image'),
                ]),
            Section::make('Publication')
                ->schema([
                    Select::make('category')->label('Catégorie')->options([
                        'association' => 'Vie de l’association',
                        'members' => 'Nouveaux membres',
                        'city' => 'Centre-ville',
                        'press' => 'Communiqués',
                    ]),
                    DateTimePicker::make('published_at')
                        ->label('Date de publication')
                        ->seconds(false)
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->locale('fr'),
                    Toggle::make('is_press_release')->label('Communiqué de presse'),
                ])->columns(2),
            Section::make('Adresse web')
                ->collapsed()
                ->schema([
                    self::localizedTabs('slug', 'Slug', false, true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Titre')->formatStateUsing(fn ($r) => $r->t('title', 'fr')),
            IconColumn::make('is_press_release')->boolean()->label('Presse'),
            TextColumn::make('published_at')->label('Publication')->date('d/m/Y'),
        ])->recordActions([
            EditAction::make()->label('Modifier'),
            DeleteAction::make()->label('Supprimer'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
