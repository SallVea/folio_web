@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="font-display text-3xl font-700 text-white mb-2">Panel Administrator</h1>
        <p class="text-muted">Kelola pengguna dan lihat statistik keseluruhan sistem.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <!-- Stat Cards -->
    <div class="bg-surface border border-border rounded-2xl p-6">
        <h3 class="text-sm font-medium text-muted mb-4">Total Pengguna</h3>
        <p class="font-display text-4xl font-700 text-white">{{ $stats['total_users'] }}</p>
    </div>
    
    <div class="bg-surface border border-border rounded-2xl p-6">
        <h3 class="text-sm font-medium text-muted mb-4">Total Portofolio</h3>
        <p class="font-display text-4xl font-700 text-white">{{ $stats['total_portfolios'] }}</p>
    </div>
    
    <div class="bg-surface border border-border rounded-2xl p-6">
        <h3 class="text-sm font-medium text-muted mb-4">Total Admin</h3>
        <p class="font-display text-4xl font-700 text-white">{{ $stats['admins'] }}</p>
    </div>
</div>

<div class="bg-surface border border-border rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-display text-xl font-600 text-white">Pengguna Terbaru</h2>
        <a href="{{ route('admin.users') }}" class="text-sm text-violetb hover:text-violet transition">Lihat Semua →</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="text-muted border-b border-border">
                <tr>
                    <th class="pb-3 font-medium">Nama</th>
                    <th class="pb-3 font-medium">Email</th>
                    <th class="pb-3 font-medium">Role</th>
                    <th class="pb-3 font-medium">Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach($recentUsers as $user)
                <tr class="hover:bg-surface2/50 transition">
                    <td class="py-4 text-white flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-violet/20 text-violetb flex items-center justify-center font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <p>{{ $user->name }}</p>
                            <p class="text-xs text-muted">{{ '@'.$user->username }}</p>
                        </div>
                    </td>
                    <td class="py-4 text-muted">{{ $user->email }}</td>
                    <td class="py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-violet/20 text-violetb' : 'bg-border text-muted' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="py-4 text-muted">{{ $user->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
