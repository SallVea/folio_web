<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'total_portfolios'   => $user->portfolios()->count(),
                'total_skills'       => $user->skills()->count(),
                'total_certificates' => $user->certificates()->count(),
                'total_views'        => $user->portfolioViews()->count(),
                'portfolio_url'      => url('/u/'.$user->username),
            ],
        ]);
    }
}
