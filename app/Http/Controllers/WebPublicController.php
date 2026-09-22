<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class WebPublicController extends Controller
{
    public function show(string $username): View
    {
        $user = User::where('username', $username)
            ->where('is_active', true)
            ->with(['portfolios.images', 'skills', 'certificates', 'socialLinks'])
            ->firstOrFail();

        // Catat view, tidak duplikat dalam 24 jam dari IP yang sama
        $viewerIp = request()->ip();
        $alreadyViewed = $user->portfolioViews()
            ->where('viewer_ip', $viewerIp)
            ->where('viewed_at', '>=', now()->subHours(24))
            ->exists();

        if (! $alreadyViewed) {
            $user->portfolioViews()->create([
                'viewer_ip' => $viewerIp,
                'viewed_at' => now(),
            ]);
        }

        return view('public-profile', compact('user'));
    }
}
