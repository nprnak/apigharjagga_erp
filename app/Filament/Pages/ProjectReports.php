<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectReports extends Page
{
    protected string $view = 'filament.pages.project-reports';

    protected static ?string $navigationLabel = 'Project Reports';

    protected static ?string $title = 'Project Reports';

    protected static ?int $navigationSort = 5;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-building-office-2';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('projects.view');
    }

    public function totalProjects(): int
    {
        return Project::count();
    }

    public function activeProjects(): int
    {
        return Project::where('status', 'in_progress')->count();
    }

    public function byStatus(): array
    {
        return Project::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }

    public function milestoneProgress(): array
    {
        return ProjectMilestone::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }

    /**
     * Budgeted (BOQ) vs actual spend (linked payment vouchers) per project
     * that has at least one of either, most over-budget first.
     *
     * @return array<int, array{name: string, budgeted: float, actual: float}>
     */
    public function budgetVsActual(): array
    {
        return Project::query()
            ->withSum('boqItems', 'amount')
            ->withSum(['paymentVouchers as vouchers_sum_amount' => fn ($q) => $q->where('status', 'paid')], 'amount')
            ->get()
            ->filter(fn (Project $p) => $p->boq_items_sum_amount > 0 || $p->vouchers_sum_amount > 0)
            ->map(fn (Project $p) => [
                'name' => $p->project_name,
                'budgeted' => (float) $p->boq_items_sum_amount,
                'actual' => (float) $p->vouchers_sum_amount,
            ])
            ->sortByDesc(fn ($row) => $row['actual'] - $row['budgeted'])
            ->values()
            ->all();
    }
}
