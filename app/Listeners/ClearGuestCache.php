<?php

namespace App\Listeners;

use App\Events\ContentChanged;
use Illuminate\Support\Facades\Cache;

class ClearGuestCache
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ContentChanged $event): void
    {
        // Increment the cache version to effectively invalidate all previous guest caches
        if (! Cache::has('guest_cache_version')) {
            Cache::put('guest_cache_version', 2);
        } else {
            Cache::increment('guest_cache_version');
        }

        // Also invalidate the command palette cache
        Cache::forget('command_palette_id');
        Cache::forget('command_palette_en');
    }
}
