<?php

namespace App\Enums;

enum Role: int
{
    case ADMIN   = 1;
    case CLIENT = 2;
    case TUTOR   = 3;

    public function label(): string
    {
        return match ($this) {
            self::ADMIN   => 'Admin',
            self::CLIENT => 'Client',
            self::TUTOR   => 'Tutor',
        };
    }

    public static function labels(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
