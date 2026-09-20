<?php

namespace App\Filament\Concerns;

use App\Services\DeepLTranslator;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

trait TranslatesFromFrench
{
    protected function translateFromFrenchAction(): Action
    {
        return Action::make('translateFromFrench')
            ->label('Traduire depuis le FR')
            ->visible(fn () => app(DeepLTranslator::class)->enabled())
            ->action(function (DeepLTranslator $translator): void {
                $record = $this->getRecord();
                $data = $this->form->getState();
                $attributes = $record->translatable ?? [];

                foreach ($attributes as $attribute) {
                    $french = data_get($data, $attribute.'.fr');
                    if (! is_string($french) || trim($french) === '') {
                        continue;
                    }
                    foreach (['nl', 'en'] as $locale) {
                        if (filled(data_get($data, $attribute.'.'.$locale))) {
                            continue;
                        }
                        data_set($data, $attribute.'.'.$locale, $translator->translate($french, $locale));
                    }
                }

                $this->form->fill($data);
                Notification::make()->title('Traductions NL/EN proposées depuis le français.')->success()->send();
            });
    }
}
