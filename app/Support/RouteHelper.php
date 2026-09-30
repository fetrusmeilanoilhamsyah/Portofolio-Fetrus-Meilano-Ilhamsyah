<?php

namespace App\Support;

class RouteHelper
{
    public static function localized(string $name, array $params = []): string
    {
        $locale = app()->getLocale();
        $routeName = ($locale === 'en' ? 'en.' : '').$name;

        return route($routeName, $params);
    }
}
