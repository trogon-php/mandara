<?php

namespace App\Enums;

enum Role: int
{
    case ADMIN              =   1;
    case CLIENT             =   2;
    case DOCTOR             =   3;
    case NURSE              =   4;
    case ATTENDANT          =   5;
    case ESTORE_DELIVERY_STAFF     =   6;

    public function label(): string
    {
        return match ($this) {
            self::ADMIN   => 'Admin',
            self::CLIENT => 'Client',
            self::DOCTOR   => 'Doctor',
            self::NURSE   => 'Nurse',
            self::ATTENDANT   => 'Attendant',
            self::ESTORE_DELIVERY_STAFF   => 'Estore Delivery Staff',
        };
    }

    public static function labels(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
