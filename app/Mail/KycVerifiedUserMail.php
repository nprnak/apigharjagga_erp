<?php

namespace App\Mail;

use App\Models\KycVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KycVerifiedUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public KycVerification $kycVerification
    ) {}

    public function envelope(): Envelope
    {
        $fromAddress = config('mail.kyc_from.address') ?: config('mail.from.address');
        $fromName = config('mail.kyc_from.name') ?: config('mail.from.name');

        return new Envelope(
            from: $fromAddress ? new Address($fromAddress, $fromName) : null,
            subject: 'Your KYC Verification Has Been Approved - API GharJagga',
        );
    }

    public function content(): Content
    {
        $dashboardUrl = url('/dashboard');

        return new Content(
            view: 'emails.kyc-verified-user',
            with: [
                'kyc' => $this->kycVerification,
                'user' => $this->kycVerification->user,
                'dashboardUrl' => $dashboardUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
