<?php

namespace App\Filament\Resources;

use App\Enums\MembershipStatus;
use App\Enums\MerchantStatus;
use App\Filament\Concerns\HasFrenchLabels;
use App\Filament\Resources\MembershipApplicationResource\Pages;
use App\Models\MembershipApplication;
use App\Models\Merchant;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MembershipApplicationResource extends Resource
{
    use HasFrenchLabels;

    protected static ?string $model = MembershipApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static string|\UnitEnum|null $navigationGroup = 'Annuaire';

    protected static ?string $modelLabel = 'Adhésion';

    protected static ?string $pluralModelLabel = 'Adhésions';

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('company_name')->label('Entreprise'),
            TextColumn::make('vat_number')->label('BCE'),
            TextColumn::make('status')->label('Statut')->badge(),
            TextColumn::make('created_at')->label('Reçue')->since(),
        ])->recordActions([
            Action::make('accept')->label('Accepter')->visible(fn ($record) => $record->status === MembershipStatus::New)
                ->action(function (MembershipApplication $record) {
                    $slug = Str::slug($record->company_name);
                    $merchant = Merchant::query()->create([
                        'name' => $record->company_name,
                        'slug' => ['fr' => $slug, 'nl' => $slug, 'en' => $slug],
                        'vat_number' => $record->vat_number,
                        'status' => MerchantStatus::Draft,
                        'member_since' => now(),
                    ]);
                    $record->update([
                        'status' => MembershipStatus::Accepted,
                        'merchant_id' => $merchant->id,
                    ]);
                    Notification::make()->title('Fiche brouillon créée')->success()->send();
                }),
            Action::make('refuse')->label('Refuser')->color('danger')
                ->visible(fn ($record) => $record->status === MembershipStatus::New)
                ->requiresConfirmation()
                ->action(fn (MembershipApplication $record) => $record->update(['status' => MembershipStatus::Refused])),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMembershipApplications::route('/'),
        ];
    }
}
