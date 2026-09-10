<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
            {{ $this->form }}
        </div>

        @php
            $cashBook = $this->cashBookRows();
            $ledger = $this->ledgerRows();
            $pnl = $this->profitAndLoss();
            $bs = $this->balanceSheet();
        @endphp

        {{-- Cash Book --}}
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-3">Cash Book</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-left text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
                            <th class="py-2 pr-3">Date</th>
                            <th class="py-2 pr-3">Description</th>
                            <th class="py-2 pr-3 text-right">Cash In</th>
                            <th class="py-2 pr-3 text-right">Cash Out</th>
                            <th class="py-2 pr-3 text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                        <tr class="text-zinc-500 dark:text-zinc-400">
                            <td class="py-2" colspan="4">Opening Balance</td>
                            <td class="py-2 text-right font-medium">Rs. {{ number_format($cashBook['opening'], 2) }}</td>
                        </tr>
                        @forelse ($cashBook['rows'] as $row)
                            <tr class="text-zinc-800 dark:text-zinc-200">
                                <td class="py-2 pr-3 whitespace-nowrap">{{ $row['date'] }}</td>
                                <td class="py-2 pr-3">{{ $row['description'] ?? '—' }}</td>
                                <td class="py-2 pr-3 text-right text-emerald-600">{{ $row['debit'] > 0 ? 'Rs. '.number_format($row['debit'], 2) : '—' }}</td>
                                <td class="py-2 pr-3 text-right text-rose-600">{{ $row['credit'] > 0 ? 'Rs. '.number_format($row['credit'], 2) : '—' }}</td>
                                <td class="py-2 pr-3 text-right font-medium">Rs. {{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-zinc-400">No cash transactions in this period.</td></tr>
                        @endforelse
                        <tr class="font-bold text-zinc-900 dark:text-zinc-100 border-t border-zinc-200 dark:border-zinc-800">
                            <td class="py-2" colspan="4">Closing Balance</td>
                            <td class="py-2 text-right">Rs. {{ number_format($cashBook['closing'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Ledger --}}
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-3">
                Ledger — {{ $ledger['account']?->account_name ?? '—' }}
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-left text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
                            <th class="py-2 pr-3">Date</th>
                            <th class="py-2 pr-3">Description</th>
                            <th class="py-2 pr-3 text-right">Debit</th>
                            <th class="py-2 pr-3 text-right">Credit</th>
                            <th class="py-2 pr-3 text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                        <tr class="text-zinc-500 dark:text-zinc-400">
                            <td class="py-2" colspan="4">Opening Balance</td>
                            <td class="py-2 text-right font-medium">Rs. {{ number_format($ledger['opening'], 2) }}</td>
                        </tr>
                        @forelse ($ledger['rows'] as $row)
                            <tr class="text-zinc-800 dark:text-zinc-200">
                                <td class="py-2 pr-3 whitespace-nowrap">{{ $row['date'] }}</td>
                                <td class="py-2 pr-3">{{ $row['description'] ?? '—' }}</td>
                                <td class="py-2 pr-3 text-right">{{ $row['debit'] > 0 ? 'Rs. '.number_format($row['debit'], 2) : '—' }}</td>
                                <td class="py-2 pr-3 text-right">{{ $row['credit'] > 0 ? 'Rs. '.number_format($row['credit'], 2) : '—' }}</td>
                                <td class="py-2 pr-3 text-right font-medium">Rs. {{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-zinc-400">No postings in this period.</td></tr>
                        @endforelse
                        <tr class="font-bold text-zinc-900 dark:text-zinc-100 border-t border-zinc-200 dark:border-zinc-800">
                            <td class="py-2" colspan="4">Closing Balance</td>
                            <td class="py-2 text-right">Rs. {{ number_format($ledger['closing'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Profit & Loss --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-3">Profit &amp; Loss</h3>
                <table class="w-full text-xs">
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                        <tr class="text-zinc-500 dark:text-zinc-400"><td class="py-1.5 font-semibold" colspan="2">Income</td></tr>
                        @foreach ($pnl['income'] as $line)
                            <tr class="text-zinc-700 dark:text-zinc-300">
                                <td class="py-1.5 pl-3">{{ $line['name'] }}</td>
                                <td class="py-1.5 text-right">Rs. {{ number_format($line['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-medium text-zinc-900 dark:text-zinc-100">
                            <td class="py-1.5">Total Income</td>
                            <td class="py-1.5 text-right">Rs. {{ number_format($pnl['totalIncome'], 2) }}</td>
                        </tr>
                        <tr class="text-zinc-500 dark:text-zinc-400"><td class="py-1.5 pt-3 font-semibold" colspan="2">Expense</td></tr>
                        @foreach ($pnl['expense'] as $line)
                            <tr class="text-zinc-700 dark:text-zinc-300">
                                <td class="py-1.5 pl-3">{{ $line['name'] }}</td>
                                <td class="py-1.5 text-right">Rs. {{ number_format($line['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-medium text-zinc-900 dark:text-zinc-100">
                            <td class="py-1.5">Total Expense</td>
                            <td class="py-1.5 text-right">Rs. {{ number_format($pnl['totalExpense'], 2) }}</td>
                        </tr>
                        <tr class="font-bold border-t border-zinc-200 dark:border-zinc-800 {{ $pnl['netProfit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            <td class="py-2">Net {{ $pnl['netProfit'] >= 0 ? 'Profit' : 'Loss' }}</td>
                            <td class="py-2 text-right">Rs. {{ number_format(abs($pnl['netProfit']), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Balance Sheet --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-3">Balance Sheet</h3>
                <table class="w-full text-xs">
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                        <tr class="text-zinc-500 dark:text-zinc-400"><td class="py-1.5 font-semibold" colspan="2">Assets</td></tr>
                        @foreach ($bs['assets'] as $line)
                            <tr class="text-zinc-700 dark:text-zinc-300">
                                <td class="py-1.5 pl-3">{{ $line['name'] }}</td>
                                <td class="py-1.5 text-right">Rs. {{ number_format($line['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-medium text-zinc-900 dark:text-zinc-100">
                            <td class="py-1.5">Total Assets</td>
                            <td class="py-1.5 text-right">Rs. {{ number_format($bs['totalAssets'], 2) }}</td>
                        </tr>

                        <tr class="text-zinc-500 dark:text-zinc-400"><td class="py-1.5 pt-3 font-semibold" colspan="2">Liabilities</td></tr>
                        @foreach ($bs['liabilities'] as $line)
                            <tr class="text-zinc-700 dark:text-zinc-300">
                                <td class="py-1.5 pl-3">{{ $line['name'] }}</td>
                                <td class="py-1.5 text-right">Rs. {{ number_format($line['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-medium text-zinc-900 dark:text-zinc-100">
                            <td class="py-1.5">Total Liabilities</td>
                            <td class="py-1.5 text-right">Rs. {{ number_format($bs['totalLiabilities'], 2) }}</td>
                        </tr>

                        <tr class="text-zinc-500 dark:text-zinc-400"><td class="py-1.5 pt-3 font-semibold" colspan="2">Equity</td></tr>
                        @foreach ($bs['equity'] as $line)
                            <tr class="text-zinc-700 dark:text-zinc-300">
                                <td class="py-1.5 pl-3">{{ $line['name'] }}</td>
                                <td class="py-1.5 text-right">Rs. {{ number_format($line['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="text-zinc-700 dark:text-zinc-300">
                            <td class="py-1.5 pl-3">Retained Earnings (cumulative)</td>
                            <td class="py-1.5 text-right">Rs. {{ number_format($bs['retainedEarnings'], 2) }}</td>
                        </tr>
                        <tr class="font-medium text-zinc-900 dark:text-zinc-100">
                            <td class="py-1.5">Total Equity</td>
                            <td class="py-1.5 text-right">Rs. {{ number_format($bs['totalEquity'], 2) }}</td>
                        </tr>
                        <tr class="font-bold border-t border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100">
                            <td class="py-2">Liabilities + Equity</td>
                            <td class="py-2 text-right">Rs. {{ number_format($bs['totalLiabilities'] + $bs['totalEquity'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
