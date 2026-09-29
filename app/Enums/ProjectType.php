<?php

namespace App\Enums;

enum ProjectType: string
{
    case Bot = 'bot';
    case Web = 'web';
    case Sistem = 'sistem';
    case Magang = 'magang';
    case Kegiatan = 'kegiatan';

    public function label(): string
    {
        return match ($this) {
            self::Bot => 'Bot',
            self::Web => 'Web',
            self::Sistem => 'Sistem',
            self::Magang => 'Magang',
            self::Kegiatan => 'Kegiatan',
        };
    }
}
