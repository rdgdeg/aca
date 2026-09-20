<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Site';

    protected static ?string $title = 'Paramètres';

    protected static ?string $slug = 'parametres';

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'admin_email' => is_array($email = Setting::get('admin_email', 'president@athinfo.be')) ? ($email['value'] ?? 'president@athinfo.be') : $email,
            'hero_image' => Setting::string('hero_image'),
            'banner' => Setting::string('banner'),
            'address' => Setting::string('address', 'Rue Ernest Cambier 2/1, 7800 Ath'),
            'facebook' => Setting::string('facebook', 'https://www.facebook.com/aca.commercantsdath/'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('admin_email')->email()->required()->label('Email admin'),
            TextInput::make('address')->label('Adresse ACA'),
            TextInput::make('facebook')->label('Page Facebook')->url(),
            TextInput::make('hero_image')->label('Image d’accueil (URL)'),
            Textarea::make('banner')->label('Bandeau d’annonce')->rows(2),
        ])->statePath('data');
    }

    public function save(): void
    {
        foreach ($this->form->getState() as $key => $value) {
            Setting::put($key, $value);
        }
        Notification::make()->title('Paramètres enregistrés')->success()->send();
    }
}
