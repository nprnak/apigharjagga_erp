<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Valuation Report {{ $report->report_no }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
        h1 { color: #0f766e; margin-bottom: 4px; }
        h2 { border-bottom: 1px solid #d1d5db; padding-bottom: 5px; margin-top: 22px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        td { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; }
        td:first-child { width: 32%; font-weight: bold; background: #f0fdfa; }
    </style>
</head>
<body>
    <h1>Property Valuation Report</h1>
    <div>Report No: {{ $report->report_no }}</div>

    <h2>Request and Property</h2>
    <table>
        <tr><td>Request Code</td><td>{{ $report->request?->request_code }}</td></tr>
        <tr><td>Client</td><td>{{ $report->request?->client?->full_name }}</td></tr>
        <tr><td>Property Code</td><td>{{ $report->property?->property_code }}</td></tr>
        <tr><td>Valuator</td><td>{{ $report->valuator?->full_name }}</td></tr>
    </table>

    <h2>Valuation</h2>
    <table>
        <tr><td>Valuation Type</td><td>{{ str_replace('_', ' ', ucwords($report->valuation_type)) }}</td></tr>
        <tr><td>Land Calculation</td><td>{{ number_format((float) $report->land_area, 4) }} × NPR {{ number_format((float) $report->land_rate, 2) }} = NPR {{ number_format((float) $report->land_area * (float) $report->land_rate, 2) }}</td></tr>
        <tr><td>Building Calculation</td><td>{{ number_format((float) $report->building_area, 4) }} × NPR {{ number_format((float) $report->building_rate, 2) }} less {{ number_format((float) $report->depreciation_percent, 2) }}% depreciation</td></tr>
        <tr><td>Other Adjustment</td><td>NPR {{ number_format((float) $report->adjustment_amount, 2) }}</td></tr>
        <tr><td>Valuated Amount</td><td>NPR {{ number_format((float) $report->valuated_amount, 2) }}</td></tr>
        <tr><td>Rate Basis / Notes</td><td>{{ $report->rate_basis ?: 'Not provided' }}</td></tr>
        <tr><td>Issued Date</td><td>{{ $report->issued_date?->format('d M Y') }}</td></tr>
    </table>

    <p>Approved by: {{ $report->approver?->full_name }}</p>
</body>
</html>
