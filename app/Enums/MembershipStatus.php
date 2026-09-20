<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MembershipStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Review = 'review';
    case Accepted = 'accepted';
    case Refused = 'refused';

    public function getLabel(): string
    {
        return $this->label();
    }

    public function label(): string
    {
        return match ($this) {
            self::New => 'Nouvelle',
            self::Review => 'En examen',
            self::Accepted => 'Acceptée',
            self::Refused => 'Refusée',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'warning',
            self::Review => 'info',
            self::Accepted => 'success',
            self::Refused => 'danger',
        };
    }
}
