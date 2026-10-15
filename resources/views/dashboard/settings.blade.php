@extends('layouts.dashboard')

@section('title', 'Pengaturan')

@section('content')
@php $user = auth()->user(); @endphp

<h1 class="font-display text-2xl font-600 mb-8">Pengaturan</h1>

<div class="grid md:grid-cols-3 gap-8">

    <!-- ─── Foto Profil ──────────────────────────── -->
    <div>
        <div class="bg-surface p-6 text-center">
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

        <div class="bg-surface p-6">
            <h2 class="font-display font-600 mb-5">Profil</h2>
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-muted font-medium block mb-1.5">Nama</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs text-muted font-medium block mb-1.5">Pekerjaan</label>
                        <input type="text" name="job_title" value="{{ $user->job_title }}" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Bio</label>
                    <textarea name="bio" rows="3" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">{{ $user->bio }}</textarea>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-muted font-medium block mb-1.5">Lokasi</label>
                        <input type="text" name="location" value="{{ $user->location }}" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs text-muted font-medium block mb-1.5">Website</label>
                        <input type="url" name="website" value="{{ $user->website }}" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                    </div>
                </div>
                <div class="border-t border-border pt-4">
                    <p class="text-xs text-muted font-medium mb-3">Tampilkan di profil publik</p>
                    <div class="flex flex-wrap gap-5 text-sm">
                        <label class="flex items-center gap-2"><input type="checkbox" name="show_portfolio" value="1" {{ $user->show_portfolio ? 'checked' : '' }}> Portfolio</label>
                        <label class="flex items-center gap-2"><input type="checkbox" name="show_skills" value="1" {{ $user->show_skills ? 'checked' : '' }}> Skills</label>
                        <label class="flex items-center gap-2"><input type="checkbox" name="show_certificates" value="1" {{ $user->show_certificates ? 'checked' : '' }}> Sertifikat</label>
                    </div>
                    <div class="mt-4">
                        <label class="text-xs text-muted font-medium block mb-1.5">Urutan section publik (pisah koma)</label>
                        <input type="text" name="section_order" value="{{ implode(',', $user->section_order ?? ['portfolio','skills','certificates']) }}" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                        <p class="text-xs text-muted mt-1">Pilihan: portfolio, skills, certificates.</p>
                    </div>
                </div>
                <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 text-sm font-medium transition">
                    Simpan Profil
                </button>
            </form>
        </div>

        <!-- ─── Username ─────────────────────────── -->
        <div class="bg-surface p-6">
            <h2 class="font-display font-600 mb-5">Username</h2>
            <form method="POST" action="{{ route('settings.username') }}" class="flex flex-wrap gap-3">
                @csrf @method('PATCH')
                <div style="display:flex; align-items:center; gap:6px; flex:1;">
                    <span class="text-sm text-muted">folio.app/</span>
                    <input type="text" name="username" id="username-input" value="{{ $user->username }}" class="flex-1 bg-bg px-4 py-2.5 text-sm focus:outline-none">
                </div>
                <button type="submit" class="bg-surface2 px-5 py-2.5 text-sm transition">Simpan</button>
            </form>
            <p id="username-status" class="text-xs mt-2 text-muted">Cek ketersediaan otomatis.</p>
        </div>

        <!-- ─── QR & Bagikan ─────────────────────── -->
        <div class="bg-surface p-6">
            <h2 class="font-display font-600 mb-5">QR & Bagikan</h2>
            @php $profileUrl = route('public.show', $user->username); @endphp
            <div class="flex flex-wrap items-center gap-6">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($profileUrl) }}" alt="QR profil" width="160" height="160">
                <div class="flex flex-col gap-2">
                    <a href="https://api.qrserver.com/v1/create-qr-code/?size=600x600&data={{ urlencode($profileUrl) }}" download="folio-{{ $user->username }}-qr.png" class="bg-surface2 px-5 py-2.5 text-sm transition">Download QR PNG</a>
                    <a href="{{ $profileUrl }}" target="_blank" class="text-sm text-violetb hover:underline">Buka profil publik</a>
                </div>
            </div>
        </div>

        <!-- ─── Hapus Akun ───────────────────────── -->
        <div class="bg-surface p-6">
            <h2 class="font-display font-600 mb-2">Hapus Akun</h2>
            <p class="text-sm text-muted mb-4">Tindakan ini permanen dan menghapus seluruh data portfolio.</p>
            <form method="POST" action="{{ route('settings.account.destroy') }}" onsubmit="return confirm('Yakin hapus akun? Tindakan ini permanen.')">
                @csrf @method('DELETE')
                <button type="submit" class="text-sm text-red-700 hover:underline">Hapus akun saya</button>
            </form>
        </div>

        <div class="bg-surface p-6">
            <h2 class="font-display font-600 mb-5">Sosial Media</h2>

            <form method="POST" action="{{ route('settings.social.store') }}" class="flex gap-3 mb-5">
                @csrf
                <select name="platform" class="bg-bg px-3 py-2.5 text-sm focus:outline-none">
                    @foreach (['GitHub','LinkedIn','Instagram','Twitter','YouTube','Facebook','Website','Other'] as $platform)
                        <option value="{{ $platform }}">{{ $platform }}</option>
                    @endforeach
                </select>
                <input type="url" name="url" id="social-url" placeholder="https://..." required class="flex-1 bg-bg px-4 py-2.5 text-sm focus:outline-none">
                <button type="submit" class="bg-surface2 px-4 py-2.5 text-sm transition">+</button>
            </form>
            <p id="social-preview" class="text-xs text-muted mb-4"></p>

            <div class="space-y-2">
                @forelse ($user->socialLinks as $link)
                    <div class="flex items-center justify-between bg-bg px-4 py-2.5">
                        <div>
                            <span class="text-xs text-[#232323]">{{ $link->platform }}</span>
                            <span class="text-xs text-muted ml-2">{{ $link->url }}</span>
                        </div>
                        <form method="POST" action="{{ route('settings.social.destroy', $link->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-muted hover:text-red-700 transition">x</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-muted">Belum ada link sosial media.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    const uname = document.getElementById('username-input');
    const ustatus = document.getElementById('username-status');
    if (uname) {
        let timer = null;
        uname.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(async () => {
                const value = uname.value.trim();
                ustatus.textContent = 'Memeriksa...';
                try {
                    const res = await fetch('{{ route('settings.username.check') }}?username=' + encodeURIComponent(value));
                    const data = await res.json();
                    ustatus.textContent = !value ? 'Cek ketersediaan otomatis.' : (data.valid ? 'Username tersedia.' : (data.taken ? 'Username sudah dipakai.' : 'Format tidak valid (3-30, huruf/angka/-_).'));
                    ustatus.style.color = data.valid ? '#166534' : '#991b1b';
                } catch (e) {
                    ustatus.textContent = 'Gagal memeriksa ketersediaan.';
                }
            }, 400);
        });
    }

    const social = document.getElementById('social-url');
    const preview = document.getElementById('social-preview');
    if (social && preview) {
        social.addEventListener('input', () => {
            try {
                const url = new URL(social.value);
                preview.textContent = 'Preview: ' + url.hostname + url.pathname;
            } catch (e) {
                preview.textContent = '';
            }
        });
    }
</script>
@endsection