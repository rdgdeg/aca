<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasFrenchLabels;
use App\Filament\Concerns\HasLocalizedFields;
use App\Filament\Resources\FormResource\Pages;
use App\Filament\Resources\FormResource\RelationManagers;
use App\Models\Form;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FormResource extends Resource
{
    use HasFrenchLabels;
    use HasLocalizedFields;

    protected static ?string $model = Form::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Messages';

    protected static ?string $modelLabel = 'Formulaire';

    protected static ?string $pluralModelLabel = 'Formulaires';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nom interne')->required(),
            TextInput::make('system_key')->label('Clé système')->unique(ignoreRecord: true),
            self::localizedTabs('title', 'Titre public', false, true),
            TagsInput::make('recipients')->label('Destinataires')->placeholder('emails@aca.be'),
            self::localizedTabs('success_message', 'Message de succès', true),
            TextInput::make('redirect_url')->label('URL de redirection'),
            Toggle::make('ack_enabled')->label('Accusé de réception')->default(true),
            self::localizedTabs('ack_subject', 'Objet accusé'),
            self::localizedTabs('ack_body', 'Texte accusé', true),
            Toggle::make('is_active')->label('Actif')->default(true),
            Repeater::make('fields')->relationship()->orderColumn('position')->schema([
                Select::make('type')->options([
                    'text' => 'Texte court',
                    'textarea' => 'Texte long',
                    'email' => 'Email',
                    'tel' => 'Téléphone',
                    'number' => 'Nombre',
                    'date' => 'Date',
                    'select' => 'Liste',
                    'radio' => 'Radio',
                    'checkbox' => 'Cases',
                    'heading' => 'Titre de section',
                    'help' => 'Texte d’aide',
                    'file' => 'Fichier',
                ])->required(),
                TextInput::make('label.fr')->label('Libellé FR')->required(),
                TextInput::make('label.nl')->label('Libellé NL'),
                TextInput::make('label.en')->label('Libellé EN'),
                Textarea::make('options.fr')->label('Options FR (une par ligne)'),
                Toggle::make('required')->label('Obligatoire'),
                Select::make('width')->label('Largeur')->options(['full' => 'Pleine', 'half' => 'Demi'])->default('full'),
            ])->addActionLabel('Ajouter un champ')->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name')->label('Formulaire'),
            TextEntry::make('system_key')->label('Clé'),
            TextEntry::make('submissions_count')
                ->label('Demandes reçues')
                ->state(fn (Form $record): int => $record->submissions()->count()),
            TextEntry::make('is_active')->label('Actif')->formatStateUsing(fn ($state) => $state ? 'Oui' : 'Non'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable(),
                TextColumn::make('system_key')->label('Clé'),
                IconColumn::make('is_active')->boolean()->label('Actif'),
                TextColumn::make('submissions_count')->counts('submissions')->label('Demandes'),
            ])
            ->recordUrl(fn (Form $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make()->label('Demandes'),
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubmissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForms::route('/'),
            'create' => Pages\CreateForm::route('/create'),
            'view' => Pages\ViewForm::route('/{record}'),
            'edit' => Pages\EditForm::route('/{record}/edit'),
        ];
    }
}
