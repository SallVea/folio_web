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
                'referrer' => substr((string) request()->headers->get('referer'), 0, 255) ?: null,
                'viewed_at' => now(),
            ]);
        }

        return view('public-profile', compact('user'));
    }

    public function vcard(string $username)
    {
        $user = User::where('username', $username)
            ->where('is_active', true)
            ->firstOrFail();

        $clean = fn (?string $value): string => trim(str_replace(["\r", "\n", "\0"], ' ', (string) $value));

        $nameParts = preg_split('/\s+/', trim($user->name) ?: 'User');
        $lastName = array_pop($nameParts);
        $firstName = implode(' ', $nameParts);

        $lines = [
            'BEGIN:VCARD',
            'VERSION:3.0',
            'FN:'.$clean($user->name),
            'N:'.$clean($lastName).';'.$clean($firstName).';;;',
            'TITLE:'.$clean($user->job_title),
            'ORG:'.$clean($user->location),
            'EMAIL:'.$clean($user->email),
            'TEL;TYPE=CELL:'.$clean($user->phone),
            'URL:'.route('public.show', $user->username),
        ];

        if ($user->website) {
            $lines[] = 'URL:'.$clean($user->website);
        }
        if ($user->bio) {
            $lines[] = 'NOTE:'.$clean($user->bio);
        }
        $lines[] = 'END:VCARD';

        return response(implode("\r\n", $lines)."\r\n", 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$user->username.'.vcf"',
        ]);
    }
}
