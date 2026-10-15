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

        // Analytics: views 7 hari terakhir
        $viewTrend = collect(range(6, 0))->map(function ($i) use ($user) {
            $date = now()->subDays($i)->toDateString();
            $count = $user->portfolioViews()->whereDate('viewed_at', $date)->count();
            return ['date' => now()->subDays($i)->format('d M'), 'count' => $count];
        })->values();

        // Analytics: views 30 hari
        $viewTrend30 = collect(range(29, 0))->map(function ($i) use ($user) {
            $date = now()->subDays($i)->toDateString();
            return ['date' => now()->subDays($i)->format('d/m'), 'count' => $user->portfolioViews()->whereDate('viewed_at', $date)->count()];
        })->values();

        // Analytics: referrer teratas
        $topReferrers = $user->portfolioViews()
            ->whereNotNull('referrer')
            ->selectRaw('referrer, count(*) as total')
            ->groupBy('referrer')
            ->orderByDesc('total')
            ->take(5)
            ->pluck('total', 'referrer');

        // Analytics: portfolio per kategori
        $portfolioByCategory = $user->portfolios()
            ->selectRaw("CASE WHEN category IS NULL OR category = '' THEN 'Tanpa Kategori' ELSE category END as cat, count(*) as total")
            ->groupBy('cat')
            ->pluck('total', 'cat');

        // Analytics: skills per level
        $skillsByLevel = $user->skills()
            ->selectRaw('level, count(*) as total')
            ->groupBy('level')
            ->pluck('total', 'level');

        $certificateReminders = $user->certificates()
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date')
            ->get();

        $profileChecks = [
            'Nama'       => filled($user->name),
            'Foto'       => filled($user->profile_photo),
            'Pekerjaan'  => filled($user->job_title),
            'Lokasi'     => filled($user->location),
            'Bio'        => filled($user->bio),
            'Portfolio'  => $stats['portfolios'] > 0,
            'Sosial'     => $user->socialLinks()->count() > 0,
        ];
        $completion = (int) round(array_sum($profileChecks) / count($profileChecks) * 100);

        return view('dashboard.home', compact('stats', 'recentPortfolios', 'profileChecks', 'completion', 'certificateReminders', 'viewTrend', 'viewTrend30', 'topReferrers', 'portfolioByCategory', 'skillsByLevel'));
    }
}
