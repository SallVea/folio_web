<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebExportController extends Controller
{
    public function export(Request $request)
    {
        $user = $request->user()->load(['portfolios.images', 'skills', 'certificates', 'socialLinks']);

        $payload = [
            'profile' => $user->only(['name', 'username', 'email', 'bio', 'job_title', 'location', 'website', 'phone']),
            'portfolios' => $user->portfolios,
            'skills' => $user->skills,
            'certificates' => $user->certificates,
            'social_links' => $user->socialLinks,
            'exported_at' => now()->toIso8601String(),
        ];

        return response()->json($payload, 200, [
            'Content-Disposition' => 'attachment; filename="folio-export-'.$user->username.'.json"',
        ]);
    }
}