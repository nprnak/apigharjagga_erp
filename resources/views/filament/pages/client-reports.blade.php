<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @include('filament.pages.partials.report-stat', ['label' => 'Total Clients', 'value' => $this->totalClients()])
            @include('filament.pages.partials.report-stat', ['label' => 'Active Clients', 'value' => $this->activeClients()])
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @include('filament.pages.partials.report-breakdown', ['title' => 'By Client Type', 'data' => $this->byType()])
            @include('filament.pages.partials.report-breakdown', [
                'title' => 'KYC Status (Web Accounts)',
                'data' => $this->byKycStatus(),
                'labelFormatter' => ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'],
            ])
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-4">New Registrations — Last 6 Months</h3>
            @php $trend = $this->registrationTrend(); $max = max(1, collect($trend)->max('total')); @endphp
            <div class="flex items-end gap-4" style="height: 140px">
                @foreach ($trend as $point)
                    <div class="flex flex-1 flex-col items-center justify-end gap-2">
                        <div
                            class="w-full rounded-t bg-brand-500"
                            style="height: {{ $point['total'] > 0 ? round(($point['total'] / $max) * 100) : 2 }}%"
                        ></div>
                        <span class="text-xs text-zinc-500">{{ $point['total'] }}</span>
                        <span class="text-[11px] text-zinc-400">{{ $point['month'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
