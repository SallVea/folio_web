@extends('layouts.dashboard')

@section('title', 'Portfolio')

@section('content')
<div x-data="{ showCreate: false }">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-2xl font-600">Portfolio</h1>
            <p class="text-muted text-sm mt-1">{{ $portfolios->count() }} karya ditampilkan</p>
        </div>
        <button @click="showCreate = !showCreate"
                class="bg-violet hover:bg-violetb text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
            <span x-text="showCreate ? '✕ Tutup' : '+ Tambah Portfolio'"></span>
        </button>
    </div>

    <!-- ─── Form Tambah ──────────────────────────── -->
    <div x-show="showCreate" x-cloak x-transition class="bg-surface border border-border rounded-xl p-6 mb-8">
        <h2 class="font-display font-600 mb-5">Portfolio Baru</h2>
        <form method="POST" action="{{ route('portfolios.store') }}" class="space-y-4">
            @csrf
            @include('dashboard.partials.portfolio-fields')
            <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                Simpan Portfolio
            </button>
        </form>
    </div>

    <!-- ─── Daftar Portfolio ─────────────────────── -->
    @if ($portfolios->isEmpty())
        <div class="bg-surface border border-border rounded-xl p-14 text-center">
            <p class="text-muted text-sm">Belum ada portfolio. Klik "+ Tambah Portfolio" di atas.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($portfolios as $p)
                <div x-data="{ editing: false }" class="bg-surface border border-border rounded-xl overflow-hidden">

                    <!-- View Mode -->
                    <div x-show="!editing" class="p-5 flex gap-4">
                        <div class="w-24 h-24 rounded-lg bg-surface2 flex-shrink-0 overflow-hidden">
                            @if ($p->thumbnail)
                                <img src="{{ \Storage::disk('public')->url($p->thumbnail) }}" class="w-full h-full object-cover" alt="">
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium">{{ $p->title }}</p>
                                    @if ($p->category)
                                        <p class="font-mono text-xs text-violetb mt-0.5">{{ $p->category }}</p>
                                    @endif
                                </div>
                                <div class="flex gap-2 flex-shrink-0">
                                    <button @click="editing = true" class="text-xs text-muted hover:text-violetb transition px-2 py-1">Edit</button>
                                    <form method="POST" action="{{ route('portfolios.destroy', $p->id) }}" onsubmit="return confirm('Hapus portfolio ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-muted hover:text-red-400 transition px-2 py-1">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            @if ($p->description)
                                <p class="text-sm text-muted mt-2 line-clamp-2">{{ $p->description }}</p>
                            @endif
                            @if ($p->tech_stack)
                                <div class="flex gap-1.5 mt-3 flex-wrap">
                                    @foreach ($p->tech_stack as $tech)
                                        <span class="font-mono text-[10px] bg-bg border border-border px-2 py-1 rounded text-muted">{{ $tech }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div x-show="editing" x-cloak class="p-5 border-b border-border">
                        <form method="POST" action="{{ route('portfolios.update', $p->id) }}" class="space-y-4">
                            @csrf @method('PUT')
                            @include('dashboard.partials.portfolio-fields', ['portfolio' => $p])
                            <div class="flex gap-3">
                                <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                                    Simpan Perubahan
                                </button>
                                <button type="button" @click="editing = false" class="text-sm text-muted hover:text-white px-5 py-2.5 transition">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Upload Thumbnail & Galeri -->
                    <div class="px-5 py-4 bg-bg/40 flex flex-wrap gap-6">
                        <form method="POST" action="{{ route('portfolios.thumbnail', $p->id) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                            @csrf
                            <label class="text-xs text-muted">Thumbnail:</label>
                            <input type="file" name="thumbnail" accept="image/*" required
                                   class="text-xs text-muted file:bg-surface2 file:border-0 file:text-violetb file:rounded file:px-3 file:py-1.5 file:mr-2 file:text-xs">
                            <button type="submit" class="text-xs bg-surface2 hover:bg-border px-3 py-1.5 rounded transition">Upload</button>
                        </form>

                        <form method="POST" action="{{ route('portfolios.images.store', $p->id) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                            @csrf
                            <label class="text-xs text-muted">Galeri ({{ $p->images->count() }}):</label>
                            <input type="file" name="images[]" accept="image/*" multiple required
                                   class="text-xs text-muted file:bg-surface2 file:border-0 file:text-violetb file:rounded file:px-3 file:py-1.5 file:mr-2 file:text-xs">
                            <button type="submit" class="text-xs bg-surface2 hover:bg-border px-3 py-1.5 rounded transition">Upload</button>
                        </form>
                    </div>

                    @if ($p->images->isNotEmpty())
                        <div class="px-5 py-4 flex gap-2 flex-wrap border-t border-border">
                            @foreach ($p->images as $img)
                                <div class="relative w-16 h-16 rounded-lg overflow-hidden group">
                                    <img src="{{ \Storage::disk('public')->url($img->image) }}" class="w-full h-full object-cover" alt="">
                                    <form method="POST" action="{{ route('portfolios.images.destroy', [$p->id, $img->id]) }}"
                                          class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center"
                                          onsubmit="return confirm('Hapus foto ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-white text-xs">✕</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
