@php
    $properties = $properties ?? collect();
@endphp

<div class="fi-wi rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800/80 dark:bg-[#0c0c0f]">
    <div class="border-b border-zinc-100 px-6 py-4 dark:border-zinc-800/80">
        <h3 class="text-sm font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Recent Property Submissions</h3>
        <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Your latest listing activity</p>
    </div>

    @if($properties->isEmpty())
        <div class="px-6 py-10 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                <svg class="h-6 w-6 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">No listings yet</p>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Complete KYC verification, then add your first property listing.</p>
            <a href="{{ url('/dashboard/my-properties/create') }}"
               class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-blue-500">
                Add Property
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-zinc-50 text-xs uppercase tracking-wider text-zinc-500 dark:bg-zinc-900/50 dark:text-zinc-400">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Reference</th>
                        <th class="px-6 py-3 font-semibold">Type</th>
                        <th class="px-6 py-3 font-semibold">Location</th>
                        <th class="px-6 py-3 font-semibold">Area</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="px-6 py-3 font-semibold">Submitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                    @foreach($properties as $property)
                        @php
                            $status = $property->approval_status;
                            $statusClass = match ($status) {
                                'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-950/60 dark:text-emerald-300',
                                'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-950/60 dark:text-amber-300',
                                'rejected' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-950/60 dark:text-rose-300',
                                default => 'bg-zinc-100 text-zinc-700 ring-zinc-300/50 dark:bg-zinc-800 dark:text-zinc-300',
                            };
                            $typeLabel = match ($property->property_type) {
                                'land' => 'Land',
                                'house' => 'House',
                                'apartment' => 'Apartment',
                                'commercial_building' => 'Commercial',
                                'office_space' => 'Office',
                                'industrial_property' => 'Industrial',
                                'agricultural_land' => 'Agricultural',
                                default => ucfirst(str_replace('_', ' ', $property->property_type)),
                            };
                        @endphp
                        <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-900/40">
                            <td class="px-6 py-3.5 font-semibold text-zinc-900 dark:text-zinc-100">{{ $property->property_code }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $typeLabel }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-zinc-600 dark:text-zinc-300">
                                {{ $property->address?->municipality ?? '—' }}
                                @if($property->address?->district)
                                    <span class="block text-xs text-zinc-400">{{ $property->address->district }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-zinc-600 dark:text-zinc-300">{{ $property->area ?? '—' }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $property->created_at?->diffForHumans() ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-100 px-6 py-3 dark:border-zinc-800/80">
            <a href="{{ url('/dashboard/my-properties') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-400">
                View all listings →
            </a>
        </div>
    @endif
</div>
