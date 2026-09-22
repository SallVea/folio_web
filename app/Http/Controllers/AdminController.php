<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Portfolio;
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
        ];

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    public function users(): View
    {
        $users = User::latest()->paginate(10);
        return view('admin.users', compact('users'));
    }
}

