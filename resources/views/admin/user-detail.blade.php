@extends('layouts.dashboard')

@section('title', 'Detail Pengguna')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="font-display text-3xl font-700">{{ $user->name }}</h1>
        <p class="text-muted text-sm mt-1">{{ '@'.$user->username }} · {{ $user->email }}</p>
    </div>
    <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-surface2 text-sm rounded-lg">← Kembali</a>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
    @foreach ([['Portfolio', $user->portfolios_count], ['Skills', $user->skills_count], ['Sertifikat', $user->certificates_count], ['Views', $user->portfolio_views_count]] as $s)
        <div class="bg-surface border border-border rounded-xl p-5">
            <p class="font-display text-3xl font-700 text-violetb">{{ $s[1] }}</p>
            <p class="text-xs text-muted mt-1">{{ $s[0] }}</p>
        </div>
    @endforeach
</div>

<div class="flex flex-wrap gap-4 mb-8">
    <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
        @csrf @method('PATCH')
        <button type="submit" class="bg-surface2 px-5 py-2.5 text-sm transition">
            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
        </button>
    </form>
    <form method="POST" action="{{ route('admin.users.role', $user->id) }}" class="flex items-center gap-2">
        @csrf @method('PATCH')
        <select name="role" class="bg-surface border border-border px-4 py-2.5 text-sm">
            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 text-sm transition">Simpan Role</button>
    </form>
</div>

<div class="bg-surface border border-border rounded-xl p-6">
    <h2 class="font-display font-600 mb-4">Audit Log</h2>
    @forelse ($logs as $log)
        <p class="text-sm py-2 border-b border-border">{{ $log->action }} · {{ $log->created_at->format('d M Y H:i') }}</p>
    @empty
        <p class="text-sm text-muted">Belum ada log untuk pengguna ini.</p>
    @endforelse
</div>
@endsection