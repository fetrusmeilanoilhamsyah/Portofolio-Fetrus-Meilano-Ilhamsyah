<?php

namespace App\Enums;

enum LinkGroup: string
{
    case Akun = 'akun';
    case Saluran = 'saluran';
    case Kontak = 'kontak';

    public function label(): string
    {
        return match ($this) {
            self::Akun => 'Akun',
            self::Saluran => 'Saluran',
            self::Kontak => 'Kontak',
        };
    }
}
