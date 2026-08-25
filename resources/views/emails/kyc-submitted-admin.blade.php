<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New KYC Verification Submitted</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 24px 12px;
            color: #1e293b;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 32px 28px;
            text-align: center;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin: 0;
        }

        .brand-sub {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-pending {
            display: inline-block;
            background-color: #fef3c7;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content {
            padding: 32px 28px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 8px;
        }

        .intro-text {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }

        .card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-top: 0;
            margin-bottom: 14px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 8px 0;
            font-size: 14px;
            vertical-align: top;
        }

        .details-label {
            color: #64748b;
            width: 38%;
            font-weight: 500;
        }

        .details-value {
            color: #0f172a;
            font-weight: 600;
        }

        .btn-container {
            text-align: center;
            margin: 32px 0 16px;
        }

        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
        }

        .btn:hover {
            background-color: #1d4ed8;
        }

        .footer {
            background-color: #f8fafc;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
        }

        .footer-note {
            margin: 0;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1 class="brand-name">API GharJagga MIS</h1>
            <div class="brand-sub">Real Estate & Property Management</div>
            <span class="badge-pending">Pending Review</span>
        </div>

        <!-- Content -->
        <div class="content">
            <h2 class="greeting">New KYC Application Submitted</h2>
            <p class="intro-text">
                A user has completed and submitted their Client KYC verification form. Please review their
                submitted documents and verify their identity record.
            </p>

            <!-- Applicant Details Card -->
            <div class="card">
                <div class="card-title">Applicant Overview</div>
                <table class="details-table">
                    <tr>
                        <td class="details-label">Full Name:</td>
                        <td class="details-value">{{ $kyc->full_name ?? $user?->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Account Email:</td>
                        <td class="details-value">{{ $user?->email ?? $kyc->email ?? 'N/A' }}</td>
                    </tr>
                    @if($kyc->mobile_no)
                        <tr>
                            <td class="details-label">Mobile Number:</td>
                            <td class="details-value">{{ $kyc->mobile_no }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="details-label">Document Type:</td>
                        <td class="details-value">{{ ucfirst(str_replace('_', ' ', $kyc->id_type ?? 'Citizenship')) }}
                        </td>
                    </tr>
                    @if($kyc->citizenship_no)
                        <tr>
                            <td class="details-label">ID / Reg No.:</td>
                            <td class="details-value">{{ $kyc->citizenship_no }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="details-label">Submitted At:</td>
                        <td class="details-value">
                            {{ $kyc->submitted_at ? $kyc->submitted_at->format('d M Y, h:i A') : now()->format('d M Y, h:i A') }}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- CTA -->
            <div class="btn-container">
                <a href="{{ $adminReviewUrl }}" class="btn">
                    Review KYC in Admin Panel &rarr;
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-note">
                This is an automated notification from <strong>API GharJagga</strong>.<br>
                Please do not reply directly to this email.
            </p>
        </div>
    </div>
</body>

</html>