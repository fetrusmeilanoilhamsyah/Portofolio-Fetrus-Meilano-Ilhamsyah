<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardShortcutsWidget extends Widget
{
    protected string $view = 'filament.widgets.dashboard-shortcuts-widget';

    protected int|string|array $columnSpan = 'full';
}
