<?php

namespace App\Observers;

use App\Mail\KycSubmittedAdminMail;
use App\Mail\KycVerifiedUserMail;
use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class KycVerificationObserver
{
    /**
     * Handle the KycVerification "created" event.
     */
    public function created(KycVerification $kyc): void
    {
        if ($kyc->status === 'pending') {
            $this->notifyAdminsOfSubmission($kyc);
        }
    }

    /**
     * Handle the KycVerification "updated" event.
     */
    public function updated(KycVerification $kyc): void
    {
        // 1. If status transitioned to approved, notify the user
        if ($kyc->wasChanged('status') && $kyc->status === 'approved') {
            $this->notifyUserOfVerification($kyc);
            return;
        }

        // 2. If status transitioned to pending, or resubmitted (submitted_at changed with pending status)
        if (($kyc->wasChanged('status') && $kyc->status === 'pending')
            || ($kyc->wasChanged('submitted_at') && $kyc->status === 'pending')) {
            $this->notifyAdminsOfSubmission($kyc);
        }
    }

    /**
     * Send email notification to all admins upon new/resubmitted KYC.
     */
    protected function notifyAdminsOfSubmission(KycVerification $kyc): void
    {
        try {
            $adminEmails = [];

            // 1. Check if ADMIN_EMAIL is explicitly set in .env / config
            $configuredAdmin = config('mail.admin_address');
            if ($configuredAdmin) {
                $configuredList = array_map('trim', explode(',', $configuredAdmin));
                $adminEmails = array_merge($adminEmails, $configuredList);
            }

            // 2. Fetch all admin users from the database
            $dbAdmins = User::query()
                ->where('role', 'admin')
                ->pluck('email')
                ->filter()
                ->all();
            $adminEmails = array_merge($adminEmails, $dbAdmins);

            $adminEmails = array_values(array_unique(array_filter($adminEmails)));

            // 3. Fallback to default sender address if no admin found
            if (empty($adminEmails)) {
                $fallback = config('mail.from.address');
                if ($fallback) {
                    $adminEmails = [$fallback];
                }
            }

            foreach ($adminEmails as $email) {
                Mail::to($email)->send(new KycSubmittedAdminMail($kyc));
            }
        } catch (\Throwable $e) {
            Log::error('Failed sending KYC submission email to admin: ' . $e->getMessage(), [
                'kyc_id' => $kyc->id,
                'exception' => $e,
            ]);
        }
    }

    /**
     * Send email notification to the user upon approval.
     */
    protected function notifyUserOfVerification(KycVerification $kyc): void
    {
        try {
            $userEmail = $kyc->user?->email ?? $kyc->email;

            if ($userEmail) {
                Mail::to($userEmail)->send(new KycVerifiedUserMail($kyc));
            }
        } catch (\Throwable $e) {
            Log::error('Failed sending KYC verified email to user: ' . $e->getMessage(), [
                'kyc_id' => $kyc->id,
                'exception' => $e,
            ]);
        }
    }
}
