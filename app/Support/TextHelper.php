<?php

namespace App\Support;

class TextHelper
{
    public static function publicText(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, '[ISI:')) {
            return null;
        }

        return $value;
    }
}
