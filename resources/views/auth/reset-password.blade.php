@extends('layouts.public')

@section('title', 'Password Baru')

@section('content')

<div class="min-h-[100svh] flex items-center justify-center px-6 py-16">
    <div class="w-full" style="max-width:460px;" data-animate>
        <a href="{{ route('home') }}" class="wordmark" style="font-size:24px; display:block; margin-bottom:40px;">Folio.</a>

        <h1 class="wordmark" style="font-size:clamp(2.5rem, 5vw, 3.5rem); margin:0 0 12px;">Password baru.</h1>
        <p style="color:var(--muted); margin:0 0 32px;">Buat password baru untuk akun Anda.</p>

        <form method="POST" action="{{ route('password.update') }}" style="display:grid; gap:24px;">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div style="display:grid; gap:8px;">
                <label for="email" style="font-size:17px;">Email</label>
                <input id="email" type="email" name="email" value="{{ request()->email }}" required readonly>
            </div>
            <div style="display:grid; gap:8px;">
                <label for="password" style="font-size:17px;">Password baru</label>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input id="password" type="password" name="password" required autofocus style="flex:1;" autocomplete="new-password" minlength="8">
                    <button type="button" onclick="togglePassword('password')" style="border:0; background:var(--surface); padding:8px 12px;" aria-label="Tampilkan atau sembunyikan password">Lihat</button>
                </div>
                @error('password')
                    <p style="color:#991b1b; font-size:14px;">{{ $message }}</p>
                @enderror
            </div>
            <div style="display:grid; gap:8px;">
                <label for="password_confirmation" style="font-size:17px;">Konfirmasi password baru</label>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input id="password_confirmation" type="password" name="password_confirmation" required style="flex:1;" autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password_confirmation')" style="border:0; background:var(--surface); padding:8px 12px;" aria-label="Tampilkan atau sembunyikan konfirmasi password">Lihat</button>
                </div>
            </div>
            <button type="submit" class="btn" style="justify-self:start;">Simpan password baru</button>
        </form>
    </div>
</div>

<script>
function togglePassword(id){
  const el=document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}
</script>
@endsection