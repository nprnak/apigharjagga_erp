<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @include('filament.pages.partials.report-stat', ['label' => 'Revenue This Month', 'value' => 'Rs. '.number_format($this->revenueThisMonth(), 2)])
            @include('filament.pages.partials.report-stat', ['label' => 'Revenue This Year', 'value' => 'Rs. '.number_format($this->revenueThisYear(), 2)])
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-4">Revenue by Category (This Year)</h3>
                @php $byCategory = $this->byCategoryThisYear(); $maxCat = count($byCategory) ? max($byCategory) : 0; @endphp
                @if (count($byCategory) === 0)
                    <p class="text-sm text-zinc-400">No income posted this year yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($byCategory as $label => $amount)
                            <div>
                                <div class="flex items-center justify-between text-xs text-zinc-600 dark:text-zinc-400">
                                    <span>{{ $label }}</span>
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">Rs. {{ number_format($amount, 2) }}</span>
                                </div>
                                <div class="mt-1 h-2 rounded-full bg-slate-100 dark:bg-zinc-800">
                                    <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $maxCat > 0 ? round(($amount / $maxCat) * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-4">Revenue Trend — Last 6 Months</h3>
                @php $trend = $this->monthlyTrend(); $maxTrend = max(1, collect($trend)->max('total')); @endphp
                <div class="flex items-end gap-4" style="height: 140px">
                    @foreach ($trend as $point)
                        <div class="flex flex-1 flex-col items-center justify-end gap-2">
                            <div
                                class="w-full rounded-t bg-emerald-500"
                                style="height: {{ $point['total'] > 0 ? round(($point['total'] / $maxTrend) * 100) : 2 }}%"
                            ></div>
                            <span class="text-[11px] text-zinc-400">{{ $point['month'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
