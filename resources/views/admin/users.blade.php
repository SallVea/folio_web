@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="font-display text-3xl font-700 text-[#232323] mb-2">Manajemen Pengguna</h1>
        <p class="text-muted">Daftar semua pengguna yang terdaftar di aplikasi.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-surface2 text-[#232323] text-sm font-medium rounded-lg hover:bg-surface transition">
        ← Kembali
    </a>
</div>

<form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap gap-3 mb-6">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, username, email..." class="flex-1 bg-surface border border-border px-4 py-2.5 text-sm focus:outline-none">
    <select name="role" class="bg-surface border border-border px-4 py-2.5 text-sm focus:outline-none">
        <option value="">Semua role</option>
        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
    </select>
    <select name="status" class="bg-surface border border-border px-4 py-2.5 text-sm focus:outline-none">
        <option value="">Semua status</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
    </select>
    <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 text-sm font-medium transition">Cari</button>
    <a href="{{ route('admin.users') }}" class="px-5 py-2.5 text-sm text-muted hover:text-[#232323] transition">Reset</a>
</form>

<div class="bg-surface border border-border rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="text-muted">
                <tr>
                    <th class="pb-3 font-medium">Nama / Username</th>
                    <th class="pb-3 font-medium">Email</th>
                    <th class="pb-3 font-medium whitespace-nowrap">Status</th>
                    <th class="pb-3 font-medium whitespace-nowrap">Role</th>
                    <th class="pb-3 font-medium whitespace-nowrap">Bergabung</th>
                    <th class="pb-3 font-medium whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="hover:bg-surface2/50 transition">
                    <td class="py-4 text-[#232323] align-middle flex items-center gap-3">
                        <div class="w-8 h-8 bg-violet/20 text-violetb flex items-center justify-center shrink-0">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <p>{{ $user->name }}</p>
                            <p class="text-xs text-muted">{{ '@'.$user->username }}</p>
                        </div>
                    </td>
                    <td class="py-4 text-muted align-middle break-all">{{ $user->email }}</td>
                    <td class="py-4 align-middle whitespace-nowrap">
                        <span class="text-xs {{ $user->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="py-4 align-middle whitespace-nowrap">
                        <span class="text-xs {{ $user->role === 'admin' ? 'bg-violet/20 text-violetb' : 'bg-border text-muted' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="py-4 text-muted align-middle whitespace-nowrap">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="py-4 align-middle whitespace-nowrap">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-xs text-violetb hover:underline">Detail</a>
                    </td>
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
