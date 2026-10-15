@extends('layouts.public')

@section('title', 'Daftar')

@section('content')

<div class="min-h-[100svh] flex items-center justify-center px-6 py-16">
    <div class="w-full" style="max-width:460px;" data-animate>
        <a href="{{ route('home') }}" class="wordmark" style="font-size:24px; display:block; margin-bottom:40px;">Folio.</a>

        <h1 class="wordmark" style="font-size:clamp(2.5rem, 5vw, 3.5rem); margin:0 0 12px;">Daftar.</h1>
        <p style="color:var(--muted); margin:0 0 32px;">Gratis, cuma beberapa menit.</p>

        @if ($errors->any())
            <p style="color:#991b1b; margin:0 0 24px;">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('register.submit') }}" style="display:grid; gap:24px;">
            @csrf
            <div style="display:grid; gap:8px;">
                <label for="name" style="font-size:17px;">Nama lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            </div>
            <div style="display:grid; gap:8px;">
                <label for="username" style="font-size:17px;">Username</label>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="color:var(--muted);">folio.app/</span>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username" autocapitalize="none" spellcheck="false">
                </div>
            </div>
            <div style="display:grid; gap:8px;">
                <label for="email" style="font-size:17px;">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" inputmode="email">
            </div>
            <div style="display:grid; gap:8px;">
                <label for="password" style="font-size:17px;">Password</label>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input id="password" type="password" name="password" required style="flex:1;" autocomplete="new-password" minlength="8">
                    <button type="button" onclick="togglePassword('password')" style="border:0; background:var(--surface); padding:8px 12px;" aria-label="Tampilkan atau sembunyikan password">Lihat</button>
                </div>
                <div id="pwd-strength" style="height:6px; background:#e9e9e9; margin-top:4px;"><div id="pwd-bar" style="height:6px; width:0%; background:#232323;"></div></div>
                <small id="pwd-label" style="color:var(--muted);">Minimal 8 karakter, kombinasikan huruf & angka.</small>
            </div>
            <div style="display:grid; gap:8px;">
                <label for="password_confirmation" style="font-size:17px;">Konfirmasi password</label>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input id="password_confirmation" type="password" name="password_confirmation" required style="flex:1;" autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password_confirmation')" style="border:0; background:var(--surface); padding:8px 12px;" aria-label="Tampilkan atau sembunyikan konfirmasi password">Lihat</button>
                </div>
            </div>
            <button type="submit" class="btn" style="justify-self:start;">Daftar</button>
        </form>

        <p style="margin-top:32px; color:var(--muted);">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
        </p>

        <section style="margin-top:56px;" data-animate>
            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:12px;">
                @foreach ([63,39,23,486] as $pid)
                    <img src="https://picsum.photos/id/{{ $pid }}/360/240"
                         alt="Still life photograph from an open collection, used as atmosphere."
                         width="360" height="240" loading="lazy" class="drift w-full"
                         style="aspect-ratio:3/2; object-fit:cover;" data-animate>
                @endforeach
            </div>
            <div style="margin-top:12px; aspect-ratio:16/9; overflow:hidden;" data-animate>
                <video autoplay muted loop playsinline preload="metadata"
                       poster="assets/hunt-and-peck-typing-monkeytype-benchmark-poster.jpg"
                       width="854" height="480" aria-hidden="true"
                       style="width:100%; height:100%; object-fit:cover;">
                    <source src="assets/hunt-and-peck-typing-monkeytype-benchmark.webm" type="video/webm">
                </video>
            </div>
        </section>
    </div>
</div>

<script>
function togglePassword(id){
  const el=document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}
const pwd=document.getElementById('password');
if(pwd){
  pwd.addEventListener('input',()=>{
    const v=pwd.value;
    let score=0;
    if(v.length>=8) score++;
    if(/[A-Z]/.test(v)) score++;
    if(/[0-9]/.test(v)) score++;
    if(/[^A-Za-z0-9]/.test(v)) score++;
    const widths=['0%','25%','50%','75%','100%'];
    document.getElementById('pwd-bar').style.width=widths[score];
    document.getElementById('pwd-label').textContent=['Lemah','Lemah','Sedang','Kuat','Sangat kuat'][score];
  });
}
</script>
@endsection