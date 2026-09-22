<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class WebDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $stats = [
            'portfolios'   => $user->portfolios()->count(),
            'skills'       => $user->skills()->count(),
            'certificates' => $user->certificates()->count(),
            'views'        => method_exists($user, 'portfolioViews') ? $user->portfolioViews()->count() : 0,
        ];

        $recentPortfolios = $user->portfolios()->latest()->take(3)->get();

        return view('dashboard.home', compact('stats', 'recentPortfolios'));
    }
}
