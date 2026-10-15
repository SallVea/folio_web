@extends('layouts.dashboard')

@section('title', 'Skills')

@section('content')
<input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
<div x-data="{ showCreate: false }">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-2xl font-600">Skills</h1>
            <p class="text-muted text-sm mt-1">{{ $skills->count() }} skill ditambahkan</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('skills.export') }}" class="px-4 py-2.5 text-sm text-muted hover:text-[#232323] transition">Export CSV</a>
            <form method="POST" action="{{ route('skills.import') }}" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" accept=".csv,.json" required class="text-xs text-muted">
                <button type="submit" class="text-xs px-3 py-2 bg-surface2 rounded transition">Impor</button>
            </form>
            <button @click="showCreate = !showCreate"
                    class="bg-violet hover:bg-violetb text-white px-5 py-2.5 text-sm font-medium transition">
                <span x-text="showCreate ? 'Tutup' : 'Tambah Skill'"></span>
            </button>
        </div>
    </div>

    <form method="GET" action="{{ route('skills.index') }}" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari skill..." class="bg-bg px-4 py-2.5 text-sm focus:outline-none">
        <select name="level" class="bg-bg px-4 py-2.5 text-sm focus:outline-none">
            <option value="">Semua level</option>
            @foreach (['Beginner','Elementary','Intermediate','Advanced','Expert'] as $lvl)
                <option value="{{ $lvl }}" {{ request('level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
            @endforeach
        </select>
        <select name="category" class="bg-bg px-4 py-2.5 text-sm focus:outline-none">
            <option value="">Semua kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 text-sm font-medium transition">Cari</button>
        <a href="{{ route('skills.index') }}" class="px-5 py-2.5 text-sm text-muted hover:text-[#232323] transition">Reset</a>
    </form>

    <div x-show="showCreate" x-cloak x-transition class="bg-surface p-6 mb-8">
        <form method="POST" action="{{ route('skills.store') }}" class="space-y-4">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Nama Skill *</label>
                    <input type="text" name="name" required class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Level</label>
                    <select name="level" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                        @foreach (['Beginner','Elementary','Intermediate','Advanced','Expert'] as $lvl)
                            <option value="{{ $lvl }}" {{ $lvl === 'Intermediate' ? 'selected' : '' }}>{{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="text-xs text-muted font-medium block mb-1.5">Kategori (opsional)</label>
                <input type="text" name="category" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
            </div>
            <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 text-sm font-medium transition">
                Simpan Skill
            </button>
        </form>
    </div>

    @php
        $levelPercent = ['Beginner' => 20, 'Elementary' => 40, 'Intermediate' => 60, 'Advanced' => 80, 'Expert' => 100];
    @endphp

    @if ($skills->isEmpty())
        <div class="bg-surface p-14 text-center">
            <p class="text-muted text-sm">Belum ada skill. Klik "Tambah Skill" di atas.</p>
        </div>
    @else
        <div class="grid md:grid-cols-2 gap-4 sortable-list" data-endpoint="{{ route('skills.reorder') }}">
            @foreach ($skills as $s)
                <div x-data="{ editing: false }" data-id="{{ $s->id }}" draggable="true" class="bg-surface p-4" style="cursor:grab;">
                    <div x-show="!editing">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-medium text-sm">{{ $s->name }}</p>
                                @if ($s->category)
                                    <p class="text-xs text-muted">{{ $s->category }}</p>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <form method="POST" action="{{ route('skills.move', $s->id) }}">
                                    @csrf
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="text-xs text-muted hover:text-[#232323] transition" title="Naik">↑</button>
                                </form>
                                <form method="POST" action="{{ route('skills.move', $s->id) }}">
                                    @csrf
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="text-xs text-muted hover:text-[#232323] transition" title="Turun">↓</button>
                                </form>
                                <button @click="editing = true" class="text-xs text-muted hover:text-[#232323] transition">Edit</button>
                                <form method="POST" action="{{ route('skills.destroy', $s->id) }}" onsubmit="return confirm('Hapus skill ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-muted hover:text-red-700 transition">Hapus</button>
                                </form>
                            </div>
                        </div>
                        <div style="height:6px; background:#e9e9e9;">
                            <div style="height:6px; width:{{ $levelPercent[$s->level] ?? 60 }}%; background:#232323;"></div>
                        </div>
                        <p class="text-xs text-muted mt-2">{{ $s->level }}</p>
                    </div>

                    <div x-show="editing" x-cloak class="space-y-4 mt-4">
                        <form method="POST" action="{{ route('skills.update', $s->id) }}" class="space-y-4">
                            @csrf @method('PUT')
                            <div>
                                <label class="text-xs text-muted font-medium block mb-1.5">Nama Skill *</label>
                                <input type="text" name="name" value="{{ $s->name }}" required class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                            </div>
                            <div>
                                <label class="text-xs text-muted font-medium block mb-1.5">Level</label>
                                <select name="level" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                                    @foreach (['Beginner','Elementary','Intermediate','Advanced','Expert'] as $lvl)
                                        <option value="{{ $lvl }}" {{ $lvl === $s->level ? 'selected' : '' }}>{{ $lvl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-muted font-medium block mb-1.5">Kategori (opsional)</label>
                                <input type="text" name="category" value="{{ $s->category }}" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                            </div>
                            @if ($s->levelHistories->isNotEmpty())
                                <div class="border-t border-border pt-3">
                                    <p class="text-xs text-muted font-medium mb-2">Riwayat Level</p>
                                    @foreach ($s->levelHistories->sortByDesc('created_at')->take(5) as $h)
                                        <p class="text-xs text-muted">
                                            {{ $h->old_level ?? 'Awal' }} → {{ $h->new_level }}
                                            <span class="ml-2">{{ $h->created_at?->format('d M Y') }}</span>
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 text-sm font-medium transition">
                                    Simpan
                                </button>
                                <button type="button" @click="editing = false" class="text-sm text-muted hover:text-[#232323] px-5 py-2.5 transition">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.sortable-list').forEach(function (list) {
            let dragged = null;
            list.querySelectorAll('[data-id]').forEach(function (item) {
                item.addEventListener('dragstart', function () {
                    dragged = item;
                    item.classList.add('opacity-50');
                });
                item.addEventListener('dragend', function () {
                    item.classList.remove('opacity-50');
                });
                item.addEventListener('dragover', function (event) {
                    event.preventDefault();
                    if (!dragged || dragged === item) {
                        return;
                    }
                    const rect = item.getBoundingClientRect();
                    const before = event.clientY < rect.top + rect.height / 2;
                    if (before) {
                        list.insertBefore(dragged, item);
                    } else {
                        list.insertBefore(dragged, item.nextSibling);
                    }
                });
                item.addEventListener('drop', function (event) {
                    event.preventDefault();
                    const ids = Array.from(list.querySelectorAll('[data-id]')).map(function (el) {
                        return parseInt(el.dataset.id, 10);
                    });
                    const token = document.getElementById('csrf-token') ? document.getElementById('csrf-token').value : '';
                    fetch(list.dataset.endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({ ids: ids, _token: token }),
                    }).then(function (response) {
                        if (response.ok) {
                            window.location.reload();
                        }
                    });
                });
            });
        });
    });
</script>
@endsection