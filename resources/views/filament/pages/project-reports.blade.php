<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @include('filament.pages.partials.report-stat', ['label' => 'Total Projects', 'value' => $this->totalProjects()])
            @include('filament.pages.partials.report-stat', ['label' => 'In Progress', 'value' => $this->activeProjects()])
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @include('filament.pages.partials.report-breakdown', [
                'title' => 'Projects by Status',
                'data' => $this->byStatus(),
                'labelFormatter' => [
                    'planning' => 'Planning', 'in_progress' => 'In Progress', 'on_hold' => 'On Hold',
                    'completed' => 'Completed', 'cancelled' => 'Cancelled',
                ],
            ])
            @include('filament.pages.partials.report-breakdown', [
                'title' => 'Milestone Progress',
                'data' => $this->milestoneProgress(),
                'labelFormatter' => ['pending' => 'Pending', 'completed' => 'Completed', 'delayed' => 'Delayed'],
            ])
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-4">Budget (BOQ) vs Actual Spend</h3>
            @php $rows = $this->budgetVsActual(); @endphp
            @if (count($rows) === 0)
                <p class="text-sm text-zinc-400">No projects with a BOQ or payment yet.</p>
            @else
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-left text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
                            <th class="py-2 pr-3">Project</th>
                            <th class="py-2 pr-3 text-right">Budgeted (BOQ)</th>
                            <th class="py-2 pr-3 text-right">Actual Spend</th>
                            <th class="py-2 pr-3 text-right">Variance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                        @foreach ($rows as $row)
                            @php $variance = $row['budgeted'] - $row['actual']; @endphp
                            <tr class="text-zinc-800 dark:text-zinc-200">
                                <td class="py-2 pr-3">{{ $row['name'] }}</td>
                                <td class="py-2 pr-3 text-right">Rs. {{ number_format($row['budgeted'], 2) }}</td>
                                <td class="py-2 pr-3 text-right">Rs. {{ number_format($row['actual'], 2) }}</td>
                                <td class="py-2 pr-3 text-right font-semibold {{ $variance < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    Rs. {{ number_format(abs($variance), 2) }} {{ $variance < 0 ? 'over' : 'under' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-filament-panels::page>
