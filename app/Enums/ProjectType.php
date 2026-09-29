<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectType: string implements HasLabel
{
    case Bot = 'bot';
    case Web = 'web';
    case Sistem = 'sistem';
    case Magang = 'magang';
    case Kegiatan = 'kegiatan';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Bot => 'Bot',
            self::Web => 'Web',
            self::Sistem => 'Sistem',
            self::Magang => 'Magang',
            self::Kegiatan => 'Kegiatan',
        };
    }

    public function label(): string
    {
        return $this->getLabel();
    }
}
