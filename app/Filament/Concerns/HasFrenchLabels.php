<?php

namespace App\Filament\Concerns;

trait HasFrenchLabels
{
    public static function hasTitleCaseModelLabel(): bool
    {
        return false;
    }
}
