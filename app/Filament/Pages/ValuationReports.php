<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\ValuationReport;
use App\Models\ValuationRequest;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ValuationReports extends Page
{
    protected string $view = 'filament.pages.valuation-reports';

    protected static ?string $navigationLabel = 'Valuation Reports';

    protected static ?string $title = 'Valuation Reports';

    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-calculator';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('valuations.view');
    }

    public function totalRequests(): int
    {
        return ValuationRequest::count();
    }

    public function totalReportsIssued(): int
    {
        return ValuationReport::where('approval_status', 'approved')->count();
    }

    public function byRequestStatus(): array
    {
        return ValuationRequest::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }

    public function byValuationType(): array
    {
        return ValuationReport::query()
            ->where('approval_status', 'approved')
            ->select('valuation_type', DB::raw('count(*) as total'))
            ->groupBy('valuation_type')
            ->orderByDesc('total')
            ->pluck('total', 'valuation_type')
            ->all();
    }

    /**
     * Average approved valuated amount per type, formatted for display.
     *
     * @return array<string, string>
     */
    public function averageAmountByType(): array
    {
        return ValuationReport::query()
            ->where('approval_status', 'approved')
            ->select('valuation_type', DB::raw('avg(valuated_amount) as avg_amount'))
            ->groupBy('valuation_type')
            ->pluck('avg_amount', 'valuation_type')
            ->map(fn ($amount) => 'Rs. '.number_format((float) $amount, 0))
            ->all();
    }

    /**
     * @return array<int, array{name: string, total: int}>
     */
    public function topValuators(): array
    {
        return ValuationReport::query()
            ->where('approval_status', 'approved')
            ->join('staff', 'staff.staff_id', '=', 'valuation_reports.valuator_staff_id')
            ->select('staff.full_name', DB::raw('count(*) as total'))
            ->groupBy('staff.full_name')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'full_name')
            ->all();
    }
}
