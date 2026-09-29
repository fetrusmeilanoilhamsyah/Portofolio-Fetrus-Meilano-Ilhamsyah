<?php

namespace App\Enums;

enum ExperienceKind: string
{
    case Kerja = 'kerja';
    case Magang = 'magang';
    case Organisasi = 'organisasi';
    case Pendidikan = 'pendidikan';

    public function label(): string
    {
        return match ($this) {
            self::Kerja => 'Kerja',
            self::Magang => 'Magang',
            self::Organisasi => 'Organisasi',
            self::Pendidikan => 'Pendidikan',
        };
    }
}
