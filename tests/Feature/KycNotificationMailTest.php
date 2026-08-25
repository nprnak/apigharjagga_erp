<?php

namespace Tests\Feature;

use App\Mail\KycSubmittedAdminMail;
use App\Mail\KycVerifiedUserMail;
use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class KycNotificationMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_email_to_admins_when_kyc_is_submitted(): void
    {
        Mail::fake();

        $admin1 = User::factory()->create([
            'email' => 'admin1@example.com',
            'role' => 'admin',
        ]);

        $admin2 = User::factory()->create([
            'email' => 'admin2@example.com',
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'name' => 'Hari Bahadur',
            'email' => 'hari@example.com',
            'role' => 'user',
        ]);

        $kyc = KycVerification::create([
            'user_id' => $user->id,
            'full_name' => 'Hari Bahadur',
            'citizenship_no' => '123-456-789',
            'id_type' => 'citizenship',
            'id_document_path' => 'kyc/documents/test.jpg',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        Mail::assertQueued(KycSubmittedAdminMail::class, function ($mail) use ($admin1, $kyc) {
            return $mail->hasTo($admin1->email) && $mail->kycVerification->id === $kyc->id;
        });

        Mail::assertQueued(KycSubmittedAdminMail::class, function ($mail) use ($admin2, $kyc) {
            return $mail->hasTo($admin2->email) && $mail->kycVerification->id === $kyc->id;
        });
    }

    public function test_sends_email_to_admins_when_kyc_is_resubmitted(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'name' => 'Sita Sharma',
            'email' => 'sita@example.com',
            'role' => 'user',
        ]);

        $kyc = KycVerification::create([
            'user_id' => $user->id,
            'full_name' => 'Sita Sharma',
            'id_type' => 'citizenship',
            'id_document_path' => 'kyc/documents/test.jpg',
            'status' => 'rejected',
            'admin_note' => 'Blurry photo',
        ]);

        Mail::fake(); // Reset fake counts

        // Resubmit KYC
        $kyc->update([
            'status' => 'pending',
            'admin_note' => null,
            'submitted_at' => now(),
        ]);

        Mail::assertQueued(KycSubmittedAdminMail::class, function ($mail) use ($admin, $kyc) {
            return $mail->hasTo($admin->email) && $mail->kycVerification->id === $kyc->id;
        });
    }

    public function test_sends_email_to_user_when_kyc_is_approved(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'Ram Shrestha',
            'email' => 'ram@example.com',
            'role' => 'user',
        ]);

        $kyc = KycVerification::create([
            'user_id' => $user->id,
            'full_name' => 'Ram Shrestha',
            'id_type' => 'citizenship',
            'id_document_path' => 'kyc/documents/test.jpg',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        Mail::fake(); // Reset fake counts

        // Admin approves KYC
        $kyc->update([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        Mail::assertQueued(KycVerifiedUserMail::class, function ($mail) use ($user, $kyc) {
            return $mail->hasTo($user->email) && $mail->kycVerification->id === $kyc->id;
        });
    }

    public function test_mailable_views_render_successfully(): void
    {
        $user = User::factory()->create([
            'name' => 'Gita Thapa',
            'email' => 'gita@example.com',
        ]);

        $kyc = KycVerification::create([
            'user_id' => $user->id,
            'full_name' => 'Gita Thapa',
            'citizenship_no' => '987-654-321',
            'id_type' => 'citizenship',
            'id_document_path' => 'kyc/documents/test.jpg',
            'status' => 'pending',
            'submitted_at' => now(),
            'reviewed_at' => now(),
        ]);

        $adminMail = new KycSubmittedAdminMail($kyc);
        $adminMail->render();

        $userMail = new KycVerifiedUserMail($kyc);
        $userMail->render();

        $this->assertTrue(true);
    }
}
