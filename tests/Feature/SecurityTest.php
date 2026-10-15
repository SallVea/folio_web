<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    private function user(bool $active = true): User
    {
        $user = User::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => Hash::make('secret123'),
            'username' => 'alice',
            'is_active' => $active,
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        return $user;
    }

    public function test_inactive_user_cannot_login_web(): void
    {
        $this->user(active: false);

        $this->post('/login', [
            'email' => 'alice@example.com',
            'password' => 'secret123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_deactivated_user_is_logged_out_middleware(): void
    {
        $user = $this->user();

        // Sesi aktif, lalu admin menonaktifkan akun.
        $this->actingAs($user);
        $user->update(['is_active' => false]);

        $this->get('/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_active_user_can_login(): void
    {
        $user = $this->user();

        $this->post('/login', [
            'email' => 'alice@example.com',
            'password' => 'secret123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_login_api(): void
    {
        $this->user(active: false);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'alice@example.com',
            'password' => 'secret123',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_credential_verify_blocks_private_host(): void
    {
        $user = $this->user();
        $cert = $user->certificates()->create([
            'title' => 'SSRF Test',
            'issuer' => 'Test',
            'credential_url' => 'https://127.0.0.1/',
        ]);

        // Status awal saat store tidak melalui checkCredentialUrl di sini.
        $cert->update(['credential_status' => 'unknown']);

        $this->actingAs($user)
            ->post('/dashboard/certificates/'.$cert->id.'/verify')
            ->assertRedirect();

        $this->assertSame('unknown', $cert->fresh()->credential_status);
    }

    public function test_javascript_url_rejected_in_social_link(): void
    {
        $this->actingAs($this->user())
            ->post('/dashboard/settings/social', [
                'platform' => 'Website',
                'url' => 'javascript://example.com/%0aalert(document.domain)',
            ])
            ->assertSessionHasErrors('url');

        $this->assertDatabaseCount('social_links', 0);
    }

    public function test_javascript_url_rejected_via_api_portfolio(): void
    {
        $this->actingAs($this->user())
            ->postJson('/api/v1/portfolios', [
                'title' => 'XSS',
                'project_url' => 'javascript://example.com/%0aalert(1)',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('project_url');
    }

    public function test_https_url_accepted_in_social_link(): void
    {
        $this->actingAs($this->user())
            ->post('/dashboard/settings/social', [
                'platform' => 'Website',
                'url' => 'https://example.com',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('social_links', 1);
    }
}
