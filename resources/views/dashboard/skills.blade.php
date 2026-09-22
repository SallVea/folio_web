@extends('layouts.dashboard')

@section('title', 'Skills')

@section('content')
<div x-data="{ showCreate: false }">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-2xl font-600">Skills</h1>
            <p class="text-muted text-sm mt-1">{{ $skills->count() }} skill ditambahkan</p>
        </div>
        <button @click="showCreate = !showCreate"
                class="bg-violet hover:bg-violetb text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
            <span x-text="showCreate ? '✕ Tutup' : '+ Tambah Skill'"></span>
        </button>
    </div>

    <div x-show="showCreate" x-cloak x-transition class="bg-surface border border-border rounded-xl p-6 mb-8">
        <form method="POST" action="{{ route('skills.store') }}" class="grid md:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label class="text-xs text-muted font-medium block mb-1.5">Nama Skill *</label>
                <input type="text" name="name" required
                       class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
            </div>
            <div>
                <label class="text-xs text-muted font-medium block mb-1.5">Level</label>
                <select name="level" class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                    @foreach (['Beginner','Elementary','Intermediate','Advanced','Expert'] as $lvl)
                        <option value="{{ $lvl }}" {{ $lvl == 'Intermediate' ? 'selected' : '' }}>{{ $lvl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <input type="text" name="category" placeholder="Kategori (opsional)"
                       class="flex-1 bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    @php
        $levelPercent = ['Beginner' => 20, 'Elementary' => 40, 'Intermediate' => 60, 'Advanced' => 80, 'Expert' => 100];
    @endphp

    @if ($skills->isEmpty())
        <div class="bg-surface border border-border rounded-xl p-14 text-center">
            <p class="text-muted text-sm">Belum ada skill. Klik "+ Tambah Skill" di atas.</p>
        </div>
    @else
        <div class="grid md:grid-cols-2 gap-3">
            @foreach ($skills as $s)
                <div class="bg-surface border border-border rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="font-medium text-sm">{{ $s->name }}</p>
                            @if ($s->category)
                                <p class="text-xs text-muted">{{ $s->category }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('skills.destroy', $s->id) }}" onsubmit="return confirm('Hapus skill ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-muted hover:text-red-400 transition">Hapus</button>
                        </form>
                    </div>
                    <div class="h-1.5 bg-bg rounded-full overflow-hidden">
                        <div class="h-full bg-violet rounded-full" style="width: {{ $levelPercent[$s->level] ?? 60 }}%"></div>
                    </div>
                    <p class="font-mono text-[10px] text-violetb mt-1.5">{{ $s->level }}</p>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
