<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use App\Notifications\CertificateExpiryReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $user = User::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => Hash::make('secret123'),
            'username' => 'alice',
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        return $user;
    }

    public function test_user_can_view_notifications_page(): void
    {
        $this->actingAs($this->user())
            ->get('/dashboard/notifications')
            ->assertStatus(200);
    }

    public function test_certificate_reminder_is_stored_as_notification(): void
    {
        $user = $this->user();
        $certificate = $user->certificates()->create([
            'title' => 'AWS',
            'issuer' => 'Amazon',
            'expiry_date' => now()->addDays(10),
        ]);

        $user->notify(new CertificateExpiryReminder($certificate));

        $this->assertDatabaseCount('notifications', 1);

        $this->actingAs($user)
            ->get('/dashboard/notifications')
            ->assertStatus(200)
            ->assertSee('Sertifikat akan kadaluarsa');
    }

    public function test_command_sends_and_marks_reminder(): void
    {
        $user = $this->user();
        $certificate = $user->certificates()->create([
            'title' => 'AWS',
            'issuer' => 'Amazon',
            'expiry_date' => now()->addDays(10),
        ]);

        $this->artisan('certificates:remind')->assertSuccessful();

        $this->assertDatabaseCount('notifications', 1);
        $this->assertNotNull($certificate->fresh()->reminder_sent_at);

        // Tidak dikirim ulang dalam 7 hari.
        $this->artisan('certificates:remind')->assertSuccessful();
        $this->assertDatabaseCount('notifications', 1);
    }
}
