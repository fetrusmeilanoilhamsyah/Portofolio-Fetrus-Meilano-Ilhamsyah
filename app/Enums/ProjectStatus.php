<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectStatus: string implements HasLabel
{
    case Draft = 'draft';
    case Published = 'published';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Terbit',
        };
    }

    public function label(): string
    {
        return $this->getLabel();
    }
}
