<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class WebOAuthController extends Controller
{
    public function redirect(string $provider)
    {
        if (! in_array($provider, ['google', 'github'])) {
            abort(404);
        }
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        if (! in_array($provider, ['google', 'github'])) {
            abort(404);
        }

        $socialUser = Socialite::driver($provider)->user();

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName() ?: $socialUser->getNickname(),
                'username' => $this->uniqueUsername($socialUser->getNickname() ?: Str::slug($socialUser->getName() ?: 'user')),
                'password' => bcrypt(Str::random(32)),
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user, true);
        return redirect()->intended('/dashboard');
    }

    private function uniqueUsername(string $base): string
    {
        $base = Str::slug($base) ?: 'user';
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }
        return $username;
    }
}