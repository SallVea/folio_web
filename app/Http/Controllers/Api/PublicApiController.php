<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicProfileResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicApiController extends Controller
{
    public function show(string $username): JsonResponse
    {
        $user = User::where('username', $username)
            ->where('is_active', true)
            ->with(['portfolios.images', 'skills', 'certificates', 'socialLinks'])
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => new PublicProfileResource($user)]);
    }

    public function recordView(Request $request, string $username): JsonResponse
    {
        $user      = User::where('username', $username)->firstOrFail();
        $viewerIp  = $request->ip();

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

        return response()->json(['success' => true, 'message' => 'View tercatat']);
    }
}
