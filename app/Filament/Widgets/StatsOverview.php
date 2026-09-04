<?php

namespace App\Filament\Widgets;

use App\Models\KycVerification;
use App\Models\Property;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Cache scalar arrays, not Eloquent models, so values are safe to unserialize.
        $kyc = Cache::remember('dashboard.stats.kyc.v2', 60, fn (): array => KycVerification::query()
            ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when status = 'approved' then 1 end) as approved")
            ->first()
            ->toArray());

        $properties = Cache::remember('dashboard.stats.properties.v2', 60, fn (): array => Property::query()
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when approval_status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when approval_status = 'approved' then 1 end) as approved")
            ->first()
            ->toArray());

        $totalUsers = Cache::remember('dashboard.stats.users.v2', 60, fn (): int => User::where('role', 'user')->count());

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('Registered user accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Pending KYC', (int) $kyc['pending'])
                ->description($kyc['approved'] . ' verified so far')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color($kyc['pending'] > 0 ? 'warning' : 'success'),

            Stat::make('Pending Listings', (int) $properties['pending'])
                ->description('Properties awaiting approval')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color($properties['pending'] > 0 ? 'warning' : 'success'),

            Stat::make('Total Properties', (int) $properties['total'])
                ->description($properties['approved'] . ' approved')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),
        ];
    }
}
