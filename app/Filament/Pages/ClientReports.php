<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\KycVerification;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientReports extends Page
{
    protected string $view = 'filament.pages.client-reports';

    protected static ?string $navigationLabel = 'Client Reports';

    protected static ?string $title = 'Client Reports';

    protected static ?int $navigationSort = 3;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('clients.view');
    }

    public function totalClients(): int
    {
        return Client::count();
    }

    public function activeClients(): int
    {
        return Client::where('is_active', true)->count();
    }

    public function byType(): array
    {
        return Client::query()
            ->select('client_type', DB::raw('count(*) as total'))
            ->groupBy('client_type')
            ->orderByDesc('total')
            ->pluck('total', 'client_type')
            ->all();
    }

    public function byKycStatus(): array
    {
        return KycVerification::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }

    /**
     * @return array<int, array{month: string, total: int}>
     */
    public function registrationTrend(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => now()->subMonths($i));

        return $months->map(function ($month) {
            $total = Client::whereYear('registration_date', $month->year)
                ->whereMonth('registration_date', $month->month)
                ->count();

            return ['month' => $month->format('M Y'), 'total' => $total];
        })->all();
    }
}
