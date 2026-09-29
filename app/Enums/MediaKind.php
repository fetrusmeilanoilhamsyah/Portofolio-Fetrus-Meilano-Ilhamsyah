<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MediaKind: string implements HasLabel
{
    case Image = 'image';
    case Video = 'video';
    case Embed = 'embed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Image => 'Gambar',
            self::Video => 'Video',
            self::Embed => 'Embed',
        };
    }

    public function label(): string
    {
        return $this->getLabel();
    }
}
