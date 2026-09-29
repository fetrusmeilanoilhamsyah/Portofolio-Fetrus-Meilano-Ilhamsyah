<?php

namespace App\Enums;

enum MediaKind: string
{
    case Image = 'image';
    case Video = 'video';
    case Embed = 'embed';

    public function label(): string
    {
        return match ($this) {
            self::Image => 'Gambar',
            self::Video => 'Video',
            self::Embed => 'Embed',
        };
    }
}
