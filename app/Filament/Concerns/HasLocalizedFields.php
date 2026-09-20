<?php

namespace App\Filament\Concerns;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

trait HasLocalizedFields
{
    public static function localizedTabs(string $name, string $label, bool $multiline = false, bool $required = false): Tabs
    {
        $tabs = [];
        foreach (['fr' => 'FR', 'nl' => 'NL', 'en' => 'EN'] as $locale => $short) {
            $field = $multiline
                ? Textarea::make("{$name}.{$locale}")->label($label.' ('.$short.')')->rows(5)->required($required && $locale === 'fr')
                : TextInput::make("{$name}.{$locale}")->label($label.' ('.$short.')')->required($required && $locale === 'fr');
            $tabs[] = Tab::make($short)->schema([$field]);
        }

        return Tabs::make($name.'_i18n')->tabs($tabs)->columnSpanFull();
    }
}
