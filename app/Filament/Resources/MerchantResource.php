<?php

namespace App\Filament\Resources;

use App\Enums\MerchantStatus;
use App\Filament\Concerns\HasCoverUpload;
use App\Filament\Concerns\HasFrenchLabels;
use App\Filament\Concerns\HasLocalizedFields;
use App\Filament\Resources\MerchantResource\Pages;
use App\Models\Merchant;
use App\Services\NominatimGeocoder;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MerchantResource extends Resource
{
    use HasCoverUpload;
    use HasFrenchLabels;
    use HasLocalizedFields;

    protected static ?string $model = Merchant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|\UnitEnum|null $navigationGroup = 'Annuaire';

    protected static ?string $modelLabel = 'Commerçant';

    protected static ?string $pluralModelLabel = 'Commerçants';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Commerce')->schema([
                TextInput::make('name')->label('Nom du commerce')->required()->maxLength(180)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        if (! $state) {
                            return;
                        }
                        $slug = Str::slug($state);
                        foreach (['fr', 'nl', 'en'] as $locale) {
                            if (! $get("slug.$locale")) {
                                $set("slug.$locale", $slug);
                            }
                        }
                    }),
                self::localizedTabs('short_text', 'Phrase résumé'),
                self::localizedTabs('description', 'Description', true),
                Select::make('categories')->label('Catégorie')->relationship('categories', 'id')->multiple()->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record?->t('name', 'fr') ?? '—')
                    ->required(),
                self::coverUpload('covers', 'Photo'),
                Select::make('status')->label('Statut')->options(collect(MerchantStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))->default(MerchantStatus::Published->value)->required(),
                Toggle::make('is_featured')->label('À la une'),
            ])->columns(1),
            Section::make('Horaires')->schema([
                Toggle::make('holiday_closed')->label('Fermé les jours fériés'),
                Toggle::make('open_sundays')->label('Ouvert le dimanche'),
                Repeater::make('openingHours')->label('Plages horaires')->relationship()->schema([
                    Select::make('weekday')->label('Jour')->options([
                        1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche',
                    ])->required(),
                    TimePicker::make('opens_at')->label('Ouverture')->seconds(false)->required(),
                    TimePicker::make('closes_at')->label('Fermeture')->seconds(false)->required(),
                ])->columns(3)->addActionLabel('Ajouter une plage'),
            ]),
            Section::make('Contact')->schema([
                TextInput::make('phone')->label('Téléphone')->tel(),
                TextInput::make('email')->label('Email')->email(),
                TextInput::make('website')->label('Site web')->url(),
                Repeater::make('socialLinks')->label('Réseaux sociaux')->relationship()->schema([
                    Select::make('network')->options([
                        'facebook' => 'Facebook', 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'linkedin' => 'LinkedIn', 'other' => 'Autre',
                    ])->required(),
                    TextInput::make('url')->url()->required(),
                ])->columns(2)->addActionLabel('Ajouter un réseau'),
            ])->columns(2),
            Section::make('Plan')->schema([
                TextInput::make('address')->label('Adresse')->required()->columnSpanFull(),
                TextInput::make('postal_code')->label('Code postal')->default('7800'),
                TextInput::make('city')->label('Ville')->default('Ath'),
                TextInput::make('lat')->numeric()->live()->label('Latitude'),
                TextInput::make('lng')->numeric()->live()->label('Longitude'),
                Actions::make([
                    Action::make('geocode')
                        ->label('Générer le GPS depuis l’adresse')
                        ->action(function (Get $get, Set $set, $livewire): void {
                            $geo = app(NominatimGeocoder::class)->geocode(trim(implode(' ', array_filter([
                                $get('address'),
                                $get('postal_code'),
                                $get('city'),
                            ]))));
                            if (! $geo) {
                                Notification::make()->title('Adresse introuvable')->danger()->send();

                                return;
                            }
                            $set('lat', $geo['lat']);
                            $set('lng', $geo['lng']);
                            $livewire->js('window.dispatchEvent(new CustomEvent("aca-gps-updated", { detail: '.json_encode($geo).' }))');
                            Notification::make()->title('Coordonnées GPS générées')->success()->send();
                        }),
                ])->columnSpanFull(),
                ViewField::make('gps_map')
                    ->label('Carte')
                    ->view('filament.forms.merchant-map')
                    ->dehydrated(false)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('status')->label('Statut')->badge(),
                IconColumn::make('is_featured')->boolean()->label('Une'),
                TextColumn::make('phone')->label('Téléphone'),
                TextColumn::make('updated_at')->since()->label('Mise à jour'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(collect(MerchantStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
            ])
            ->recordActions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMerchants::route('/'),
            'create' => Pages\CreateMerchant::route('/create'),
            'edit' => Pages\EditMerchant::route('/{record}/edit'),
        ];
    }

    public static function fillSlug(array $data, ?Merchant $record = null): array
    {
        $slug = Str::slug($data['name'] ?? $record?->name ?? '');
        $data['slug'] = [
            'fr' => $data['slug']['fr'] ?? $record?->getTranslation('slug', 'fr') ?: $slug,
            'nl' => $data['slug']['nl'] ?? $record?->getTranslation('slug', 'nl') ?: $slug,
            'en' => $data['slug']['en'] ?? $record?->getTranslation('slug', 'en') ?: $slug,
        ];

        return $data;
    }

    public static function geocodeIfNeeded(Merchant $merchant): void
    {
        if ($merchant->lat && $merchant->lng) {
            return;
        }

        $geo = app(NominatimGeocoder::class)->geocode(trim($merchant->address.' '.$merchant->postal_code.' '.$merchant->city));
        if ($geo) {
            $merchant->forceFill($geo)->saveQuietly();
        }
    }
}
