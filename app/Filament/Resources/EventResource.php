<?php

namespace App\Filament\Resources;

use App\Enums\PublishStatus;
use App\Filament\Concerns\HasCoverUpload;
use App\Filament\Concerns\HasFrenchLabels;
use App\Filament\Concerns\HasLocalizedFields;
use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    use HasCoverUpload;
    use HasFrenchLabels;
    use HasLocalizedFields;

    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Centre-ville';

    protected static ?string $modelLabel = 'Événement';

    protected static ?string $pluralModelLabel = 'Événements';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contenu')
                ->description('Le français est obligatoire. Les traductions NL/EN peuvent rester vides.')
                ->schema([
                    self::localizedTabs('title', 'Titre', false, true),
                    self::localizedTabs('description', 'Description', true),
                    self::coverUpload('events', 'Affiche'),
                ]),
            Section::make('Adresse web')
                ->description('Générée automatiquement à partir du titre français si elle est vide.')
                ->collapsed()
                ->schema([
                    self::localizedTabs('slug', 'Slug'),
                ]),
            Section::make('Quand et où')
                ->schema([
                    DateTimePicker::make('starts_at')
                        ->label('Début')
                        ->required()
                        ->seconds(false)
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->locale('fr'),
                    DateTimePicker::make('ends_at')
                        ->label('Fin')
                        ->seconds(false)
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->locale('fr'),
                    TextInput::make('location')->label('Lieu')->columnSpanFull(),
                ])->columns(2),
            Section::make('Organisation')
                ->schema([
                    Select::make('event_type_id')
                        ->label('Type')
                        ->relationship('type', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record?->t('name', 'fr') ?? '—')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Select::make('merchant_id')
                        ->label('Commerçant associé')
                        ->relationship('merchant', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    TextInput::make('price')->label('Prix'),
                    TextInput::make('external_url')->label('Lien externe')->url(),
                    Select::make('status')
                        ->label('Statut')
                        ->options(collect(PublishStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()]))
                        ->default(PublishStatus::Published->value)
                        ->required(),
                    Toggle::make('is_featured')->label('Mettre en avant'),
                ])->columns(2),
            Section::make('Inscriptions')
                ->collapsed()
                ->schema([
                    Select::make('form_id')
                        ->label('Formulaire d’inscription')
                        ->relationship('form', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    TextInput::make('capacity')->label('Capacité')->numeric()->minValue(1),
                    DateTimePicker::make('registration_deadline')
                        ->label('Clôture des inscriptions')
                        ->seconds(false)
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->locale('fr'),
                    Toggle::make('waitlist')->label('Liste d’attente si complet'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->formatStateUsing(fn ($record) => $record->t('title', 'fr'))
                    ->searchable()
                    ->wrap(),
                TextColumn::make('starts_at')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('location')->label('Lieu')->toggleable(),
                IconColumn::make('is_featured')->boolean()->label('En avant'),
                TextColumn::make('status')->label('Statut')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(PublishStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()])),
            ])
            ->recordActions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function fillSlug(array $data, ?Event $record = null): array
    {
        $source = $data['title']['fr'] ?? $record?->getTranslation('title', 'fr') ?? '';
        $slug = Str::slug((string) $source);

        $data['slug'] = [
            'fr' => $data['slug']['fr'] ?? $record?->getTranslation('slug', 'fr') ?: $slug,
            'nl' => $data['slug']['nl'] ?? $record?->getTranslation('slug', 'nl') ?: $slug,
            'en' => $data['slug']['en'] ?? $record?->getTranslation('slug', 'en') ?: $slug,
        ];

        return $data;
    }
}
