<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PortfolioTest extends TestCase
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

    public function test_user_can_create_portfolio(): void
    {
        $this->actingAs($this->user())
            ->post('/dashboard/portfolios', [
                'title' => 'Project Satu',
                'category' => 'Web',
                'tech_stack_input' => 'Laravel, Vue',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('portfolios', ['title' => 'Project Satu', 'category' => 'Web']);
    }

    public function test_portfolio_requires_title(): void
    {
        $this->actingAs($this->user())
            ->post('/dashboard/portfolios', ['title' => ''])
            ->assertSessionHasErrors('title');
    }

    public function test_portfolio_rejects_non_https_url(): void
    {
        $this->actingAs($this->user())
            ->post('/dashboard/portfolios', [
                'title' => 'Project',
                'project_url' => 'http://example.com',
            ])
            ->assertSessionHasErrors('project_url');
    }
}