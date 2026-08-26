<?php

namespace App\Filament\Widgets;

use App\Models\KycVerification;
use App\Models\Property;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pendingKyc      = KycVerification::where('status', 'pending')->count();
        $approvedKyc     = KycVerification::where('status', 'approved')->count();
        $pendingListings = Property::where('approval_status', 'pending')->count();
        $totalUsers      = User::where('role', 'user')->count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('Registered user accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Pending KYC', $pendingKyc)
                ->description($approvedKyc . ' verified so far')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color($pendingKyc > 0 ? 'warning' : 'success'),

            Stat::make('Pending Listings', $pendingListings)
                ->description('Properties awaiting approval')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color($pendingListings > 0 ? 'warning' : 'success'),

            Stat::make('Total Properties', Property::count())
                ->description(Property::where('approval_status', 'approved')->count() . ' approved')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),
        ];
    }
}
