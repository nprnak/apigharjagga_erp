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

class KycSubmittedAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public KycVerification $kycVerification
    ) {}

    public function envelope(): Envelope
    {
        $applicantName = $this->kycVerification->full_name 
            ?? $this->kycVerification->user?->name 
            ?? 'New Applicant';

        $fromAddress = config('mail.kyc_from.address') ?: config('mail.from.address');
        $fromName = config('mail.kyc_from.name') ?: config('mail.from.name');

        return new Envelope(
            from: $fromAddress ? new Address($fromAddress, $fromName) : null,
            subject: "[API GharJagga] New KYC Verification Submitted - {$applicantName}",
        );
    }

    public function content(): Content
    {
        $adminReviewUrl = url('/admin/kyc-verifications/' . $this->kycVerification->id);

        return new Content(
            view: 'emails.kyc-submitted-admin',
            with: [
                'kyc' => $this->kycVerification,
                'user' => $this->kycVerification->user,
                'adminReviewUrl' => $adminReviewUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
