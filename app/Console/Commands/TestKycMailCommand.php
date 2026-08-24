<?php

namespace App\Console\Commands;

use App\Mail\KycSubmittedAdminMail;
use App\Mail\KycVerifiedUserMail;
use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestKycMailCommand extends Command
{
    protected $signature = 'kyc:test-mail {--admin=sudinstha4@gmail.com} {--user=venture.sudin@gmail.com}';

    protected $description = 'Send real test KYC emails to admin and user to verify live SMTP delivery';

    public function handle(): int
    {
        $adminEmail = $this->option('admin') ?: config('mail.admin_address') ?: 'sudinstha4@gmail.com';
        $userEmail = $this->option('user') ?: 'venture.sudin@gmail.com';

        $this->info("--------------------------------------------------");
        $this->info("   API GharJagga - Live KYC Email Tester");
        $this->info("--------------------------------------------------");
        $this->info("Admin Recipient  : {$adminEmail}");
        $this->info("User Recipient   : {$userEmail}");
        $this->info("Sender (From)    : " . config('mail.from.address') . " <" . config('mail.from.name') . ">");
        $this->info("SMTP Host        : " . config('mail.mailers.smtp.host') . ":" . config('mail.mailers.smtp.port'));
        $this->info("--------------------------------------------------");

        // Create a mock user & KYC record for the test
        $user = new User([
            'name' => 'Sudin Shrestha (Applicant)',
            'email' => $userEmail,
            'role' => 'user',
        ]);
        $user->id = 9999;

        $kyc = new KycVerification([
            'user_id' => $user->id,
            'full_name' => 'Sudin Shrestha',
            'citizenship_no' => '27-01-79-12345',
            'id_type' => 'citizenship',
            'mobile_no' => '+977 9800000000',
            'email' => $userEmail,
            'status' => 'pending',
            'submitted_at' => now(),
            'reviewed_at' => now(),
        ]);
        $kyc->id = 8888;
        $kyc->setRelation('user', $user);

        // 1. Send KYC Submission Alert to Admin
        $this->comment("1. Sending [KycSubmittedAdminMail] to Admin ({$adminEmail})...");
        try {
            Mail::to($adminEmail)->sendNow(new KycSubmittedAdminMail($kyc));
            $this->info("   [SUCCESS] KYC Submission email sent successfully to Admin ({$adminEmail})!");
        } catch (\Throwable $e) {
            $this->error("   [FAILED] Failed sending KYC Submission email to Admin.");
            $this->error("   Error: " . $e->getMessage());
            $this->renderSmtpHelp($e->getMessage());
            return self::FAILURE;
        }

        // 2. Send KYC Verification Approval Notice to User
        $this->comment("2. Sending [KycVerifiedUserMail] to User ({$userEmail})...");
        try {
            $kyc->status = 'approved';
            Mail::to($userEmail)->sendNow(new KycVerifiedUserMail($kyc));
            $this->info("   [SUCCESS] KYC Verification approval email sent successfully to User ({$userEmail})!");
        } catch (\Throwable $e) {
            $this->error("   [FAILED] Failed sending KYC Verification approval email to User.");
            $this->error("   Error: " . $e->getMessage());
            $this->renderSmtpHelp($e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info("==================================================");
        $this->info(" All test emails successfully delivered via SMTP!");
        $this->info(" - Admin Email received at: {$adminEmail}");
        $this->info(" - User Email received at : {$userEmail}");
        $this->info("==================================================");

        return self::SUCCESS;
    }

    protected function renderSmtpHelp(string $errorMessage): void
    {
        if (str_contains($errorMessage, '535') || str_contains($errorMessage, 'Username and Password not accepted') || str_contains($errorMessage, 'Authentication failed')) {
            $this->newLine();
            $this->warn("=================== GMAIL SMTP NOTICE ===================");
            $this->line("Gmail requires a 16-character 'Google App Password' rather than your normal password.");
            $this->line("1. Visit: https://myaccount.google.com/apppasswords");
            $this->line("2. Generate a new App Password for 'Laravel/API GharJagga'.");
            $this->line("3. Update .env: MAIL_PASSWORD=\"your16digitcode\"");
            $this->line("4. Run: php artisan config:clear");
            $this->warn("=========================================================");
            $this->newLine();
        }
    }
}
