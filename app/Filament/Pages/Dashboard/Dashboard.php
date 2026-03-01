<?php

namespace App\Filament\Pages\Dashboard;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    protected static string $view = 'filament.pages.dashboard';
}
