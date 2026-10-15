<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Portfolio;
use App\Models\PortfolioView;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_portfolios' => Portfolio::count(),
            'admins' => User::where('role', 'admin')->count(),
            'total_views' => PortfolioView::count(),
        ];

        $recentUsers = User::latest()->take(5)->get();

        $registrationTrend = collect(range(6, 0))->map(function ($i) {
            $date = now()->subDays($i)->toDateString();
            return ['date' => now()->subDays($i)->format('d M'), 'count' => User::whereDate('created_at', $date)->count()];
        })->values();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'registrationTrend'));
    }

    public function users(Request $request): View
    {
        $query = User::query();

        if ($q = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', '%'.$q.'%')
                    ->orWhere('username', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%');
            });
        }

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->query('status')) {
            $query->where('is_active', $status === 'active');
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function showUser(int $id): View
    {
        $user = User::withCount(['portfolios', 'skills', 'certificates', 'portfolioViews'])->findOrFail($id);
        $logs = AuditLog::where('user_id', $user->id)->latest()->take(20)->get();

        return view('admin.user-detail', compact('user', 'logs'));
    }

    public function toggleActive(int $id): \Illuminate\Http\RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);

        // Cabut semua token API saat akun dinonaktifkan.
        if (! $user->is_active) {
            $user->tokens()->delete();
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $user->is_active ? 'admin.user.activate' : 'admin.user.deactivate',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'ip' => request()->ip(),
        ]);

        return back()->with('success', 'Status pengguna diperbarui.');
    }

    public function updateRole(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate(['role' => 'required|in:admin,user']);
        $user = User::findOrFail($id);
        $user->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin.user.role',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'meta' => ['role' => $validated['role']],
            'ip' => request()->ip(),
        ]);

        return back()->with('success', 'Role pengguna diperbarui.');
    }

    public function auditLogs(): View
    {
        $logs = AuditLog::with('user')->latest()->paginate(20);

        return view('admin.audit-logs', compact('logs'));
    }
}