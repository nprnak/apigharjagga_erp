{{-- Shared by every Reports page: a titled card showing a label => count
     breakdown as horizontal bars, sized relative to the largest value. --}}
@php
    $max = count($data) ? max($data) : 0;
    $labelFormatter = $labelFormatter ?? [];
@endphp
<div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-4">{{ $title }}</h3>
    @if (count($data) === 0)
        <p class="text-sm text-zinc-400">No data yet.</p>
    @else
        <div class="space-y-3">
            @foreach ($data as $label => $count)
                <div>
                    <div class="flex items-center justify-between text-xs text-zinc-600 dark:text-zinc-400">
                        <span>{{ $labelFormatter[$label] ?? ucwords(str_replace('_', ' ', (string) $label)) }}</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $count }}</span>
                    </div>
                    <div class="mt-1 h-2 rounded-full bg-slate-100 dark:bg-zinc-800">
                        <div
                            class="h-2 rounded-full bg-brand-500"
                            style="width: {{ $max > 0 ? round(($count / $max) * 100) : 0 }}%"
                        ></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
