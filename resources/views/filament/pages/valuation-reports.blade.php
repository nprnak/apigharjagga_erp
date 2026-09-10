<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @include('filament.pages.partials.report-stat', ['label' => 'Total Requests', 'value' => $this->totalRequests()])
            @include('filament.pages.partials.report-stat', ['label' => 'Reports Issued', 'value' => $this->totalReportsIssued()])
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @include('filament.pages.partials.report-breakdown', [
                'title' => 'Requests by Status',
                'data' => $this->byRequestStatus(),
                'labelFormatter' => [
                    'received' => 'Received', 'site_visit_scheduled' => 'Site Visit Scheduled',
                    'in_progress' => 'In Progress', 'report_issued' => 'Report Issued', 'cancelled' => 'Cancelled',
                ],
            ])
            @include('filament.pages.partials.report-breakdown', ['title' => 'Issued Reports by Type', 'data' => $this->byValuationType()])
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-4">Average Valuated Amount by Type</h3>
                @php $avg = $this->averageAmountByType(); @endphp
                @if (count($avg) === 0)
                    <p class="text-sm text-zinc-400">No approved reports yet.</p>
                @else
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                            @foreach ($avg as $type => $amount)
                                <tr>
                                    <td class="py-2 text-zinc-600 dark:text-zinc-400">{{ ucwords(str_replace('_', ' ', $type)) }}</td>
                                    <td class="py-2 text-right font-semibold text-zinc-900 dark:text-zinc-100">{{ $amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-4">Top Valuators</h3>
                @php $top = $this->topValuators(); @endphp
                @if (count($top) === 0)
                    <p class="text-sm text-zinc-400">No approved reports yet.</p>
                @else
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                            @foreach ($top as $name => $total)
                                <tr>
                                    <td class="py-2 text-zinc-600 dark:text-zinc-400">{{ $name }}</td>
                                    <td class="py-2 text-right font-semibold text-zinc-900 dark:text-zinc-100">{{ $total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
