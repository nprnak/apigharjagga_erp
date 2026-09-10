<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
        h1 { color: #0f766e; margin-bottom: 4px; }
        h2 { border-bottom: 1px solid #d1d5db; padding-bottom: 5px; margin-top: 22px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        td, th { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; text-align: left; }
        th { background: #f0fdfa; }
        .meta td:first-child { width: 32%; font-weight: bold; background: #f0fdfa; }
        .totals td:first-child { width: 80%; text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Invoice</h1>
    <div>Invoice No: {{ $invoice->invoice_no }}</div>

    <h2>Bill To</h2>
    <table class="meta">
        <tr><td>Client</td><td>{{ $invoice->client?->full_name ?? '—' }}</td></tr>
        <tr><td>Property</td><td>{{ $invoice->property?->property_code ?? '—' }}</td></tr>
        <tr><td>Issue Date</td><td>{{ $invoice->issue_date?->format('d M Y') }}</td></tr>
        <tr><td>Due Date</td><td>{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td></tr>
        <tr><td>Status</td><td>{{ ucfirst($invoice->status) }}</td></tr>
    </table>

    <h2>Items</h2>
    <table>
        <tr>
            <th>Description</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Amount</th>
        </tr>
        @foreach ($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td>{{ number_format((float) $item->quantity, 2) }}</td>
                <td>NPR {{ number_format((float) $item->unit_price, 2) }}</td>
                <td>NPR {{ number_format((float) $item->amount, 2) }}</td>
            </tr>
        @endforeach
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td>NPR {{ number_format((float) $invoice->subtotal, 2) }}</td></tr>
        <tr><td>Tax</td><td>NPR {{ number_format((float) $invoice->tax_amount, 2) }}</td></tr>
        <tr><td>Total</td><td>NPR {{ number_format((float) $invoice->total_amount, 2) }}</td></tr>
    </table>

    @if ($invoice->notes)
        <h2>Notes</h2>
        <p>{{ $invoice->notes }}</p>
    @endif
</body>
</html>
