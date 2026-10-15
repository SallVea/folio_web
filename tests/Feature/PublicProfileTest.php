<?php
namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_profile_renders(): void
    {
        $user = User::create(['name'=>'Alice','email'=>'alice@example.com','password'=>bcrypt('secret'),'username'=>'alice','is_active'=>true]);
        $this->get('/alice')->assertStatus(200);
    }

    public function test_vcard_download(): void
    {
        $user = User::create(['name'=>'Alice','email'=>'alice@example.com','password'=>bcrypt('secret'),'username'=>'alice','is_active'=>true]);
        $this->get('/alice/vcard')->assertStatus(200);
    }
}