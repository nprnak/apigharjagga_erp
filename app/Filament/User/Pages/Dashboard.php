<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\KycStatusWidget;
use App\Filament\User\Widgets\ListingsStatusChart;
use App\Filament\User\Widgets\QuickActionsWidget;
use App\Filament\User\Widgets\RecentListingsWidget;
use App\Filament\User\Widgets\UserStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    protected string $view = 'filament.user.pages.dashboard';

    public function getHeading(): string
    {
        return '';
    }

    public function getWidgets(): array
    {
        return [];
    }
}
