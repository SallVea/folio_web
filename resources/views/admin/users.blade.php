@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="font-display text-3xl font-700 text-white mb-2">Manajemen Pengguna</h1>
        <p class="text-muted">Daftar semua pengguna yang terdaftar di aplikasi.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-surface2 text-white text-sm font-medium rounded-lg hover:bg-surface transition">
        ← Kembali
    </a>
</div>

<div class="bg-surface border border-border rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="text-muted border-b border-border">
                <tr>
                    <th class="pb-3 font-medium">Nama / Username</th>
                    <th class="pb-3 font-medium">Email</th>
                    <th class="pb-3 font-medium">Status</th>
                    <th class="pb-3 font-medium">Role</th>
                    <th class="pb-3 font-medium">Bergabung</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach($users as $user)
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
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
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
    
    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
