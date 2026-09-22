@extends('layouts.dashboard')

@section('title', 'Pengaturan')

@section('content')
@php $user = auth()->user(); @endphp

<h1 class="font-display text-2xl font-600 mb-8">Pengaturan</h1>

<div class="grid md:grid-cols-3 gap-8">

    <!-- ─── Foto Profil ──────────────────────────── -->
    <div>
        <div class="bg-surface border border-border rounded-xl p-6 text-center">
            <div class="w-24 h-24 rounded-full bg-violet mx-auto mb-4 overflow-hidden flex items-center justify-center">
                @if ($user->profile_photo)
                    <img src="{{ \Storage::disk('public')->url($user->profile_photo) }}" class="w-full h-full object-cover" alt="">
                @else
                    <span class="font-display text-3xl font-700 text-white">{{ substr($user->name, 0, 1) }}</span>
                @endif
            </div>
            <form method="POST" action="{{ route('settings.photo') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="photo" accept="image/*" id="photo-input" class="hidden" onchange="this.form.submit()">
                <label for="photo-input" class="cursor-pointer text-xs text-violetb hover:underline block">Ganti Foto</label>
                @error('photo')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </form>
        </div>
    </div>

    <!-- ─── Profil & Sosmed ──────────────────────── -->
    <div class="md:col-span-2 space-y-8">

        <div class="bg-surface border border-border rounded-xl p-6">
            <h2 class="font-display font-600 mb-5">Profil</h2>
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-muted font-medium block mb-1.5">Nama</label>
                        <input type="text" name="name" value="{{ $user->name }}"
                               class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                    </div>
                    <div>
                        <label class="text-xs text-muted font-medium block mb-1.5">Pekerjaan</label>
                        <input type="text" name="job_title" value="{{ $user->job_title }}"
                               class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                    </div>
                </div>
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Bio</label>
                    <textarea name="bio" rows="3"
                              class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">{{ $user->bio }}</textarea>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-muted font-medium block mb-1.5">Lokasi</label>
                        <input type="text" name="location" value="{{ $user->location }}"
                               class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                    </div>
                    <div>
                        <label class="text-xs text-muted font-medium block mb-1.5">Website</label>
                        <input type="url" name="website" value="{{ $user->website }}"
                               class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                    </div>
                </div>
                <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    Simpan Profil
                </button>
            </form>
        </div>

        <div class="bg-surface border border-border rounded-xl p-6">
            <h2 class="font-display font-600 mb-5">Sosial Media</h2>

            <form method="POST" action="{{ route('settings.social.store') }}" class="flex gap-3 mb-5">
                @csrf
                <select name="platform" class="bg-bg border border-border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-violet">
                    @foreach (['GitHub','LinkedIn','Instagram','Twitter','YouTube','Facebook','Website','Other'] as $platform)
                        <option value="{{ $platform }}">{{ $platform }}</option>
                    @endforeach
                </select>
                <input type="url" name="url" placeholder="https://..." required
                       class="flex-1 bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                <button type="submit" class="bg-surface2 hover:bg-border px-4 py-2.5 rounded-lg text-sm transition">+</button>
            </form>

            <div class="space-y-2">
                @forelse ($user->socialLinks as $link)
                    <div class="flex items-center justify-between bg-bg border border-border rounded-lg px-4 py-2.5">
                        <div>
                            <span class="text-xs font-mono text-violetb">{{ $link->platform }}</span>
                            <span class="text-xs text-muted ml-2">{{ $link->url }}</span>
                        </div>
                        <form method="POST" action="{{ route('settings.social.destroy', $link->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-muted hover:text-red-400 transition">✕</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-muted">Belum ada link sosial media.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
