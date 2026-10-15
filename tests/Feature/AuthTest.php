<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_user_can_register_and_is_redirected_to_verification(): void
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'username' => 'newuser',
            'email' => 'new@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/email/verify');
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }

    public function test_verified_user_can_login(): void
    {
        $user = User::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => Hash::make('secret123'),
            'username' => 'alice',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'alice@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => Hash::make('secret123'),
            'username' => 'alice',
        ]);

        $response = $this->post('/login', [
            'email' => 'alice@example.com',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_unverified_user_cannot_access_dashboard(): void
    {
        $user = User::create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => Hash::make('secret123'),
            'username' => 'bob',
        ]);

        $this->actingAs($user)->get('/dashboard')->assertRedirect('/email/verify');
    }

    public function test_verification_notice_page_renders(): void
    {
        $user = User::create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => Hash::make('secret123'),
            'username' => 'bob',
        ]);

        $this->actingAs($user)->get('/email/verify')->assertStatus(200);
    }

    public function test_export_endpoint_works(): void
    {
        $user = User::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => Hash::make('secret123'),
            'username' => 'alice',
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        $this->actingAs($user)->get('/dashboard/export')->assertStatus(200);
    }

    public function test_dashboard_renders_with_notification_badge(): void
    {
        $user = User::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => Hash::make('secret123'),
            'username' => 'alice',
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        $this->actingAs($user)->get('/dashboard')->assertStatus(200)->assertSee('Notifikasi');
    }
}