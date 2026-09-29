<?php

namespace App\Models\Concerns;

use App\Events\ContentChanged;

trait DispatchesContentChanged
{
    protected static function bootDispatchesContentChanged(): void
    {
        $dispatch = fn () => event(new ContentChanged);

        static::created($dispatch);
        static::updated($dispatch);
        static::deleted($dispatch);
    }
}
