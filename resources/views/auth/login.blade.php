@extends('layouts.public')

@section('title', 'Masuk')

@section('content')

<div class="min-h-[100svh] flex items-center justify-center px-6 py-16">
    <div class="w-full" style="max-width:420px;" data-animate>
        <a href="{{ route('home') }}" class="wordmark" style="font-size:24px; display:block; margin-bottom:40px;">Folio.</a>

        <h1 class="wordmark" style="font-size:clamp(3rem, 8vw, 5rem); margin:0 0 12px;">Masuk.</h1>
        <p style="color:var(--muted); margin:0 0 32px;">Kelola portofolio kamu.</p>

        @if ($errors->any())
            <p style="color:#991b1b; margin:0 0 24px;">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" style="display:grid; gap:24px;">
            @csrf
            <div style="display:grid; gap:8px;">
                <label for="email" style="font-size:17px;">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" inputmode="email">
            </div>
            <div style="display:grid; gap:8px;">
                <div style="display:flex; justify-content:space-between; align-items:baseline; gap:12px;">
                    <label for="password" style="font-size:17px;">Password</label>
                    <a href="{{ route('password.request') }}" style="font-size:17px; color:var(--muted);">Lupa password</a>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input id="password" type="password" name="password" required style="flex:1;" autocomplete="current-password">
                    <button type="button" onclick="togglePassword('password')" style="border:0; background:var(--surface); padding:8px 12px;" aria-label="Tampilkan atau sembunyikan password">Lihat</button>
                </div>
            </div>
            <button type="submit" class="btn" style="justify-self:start;">Masuk</button>
        </form>
        <div class="mt-6 flex flex-col gap-3">
            <a href="{{ route('oauth.redirect', 'google') }}" class="btn w-full text-center">Login dengan Google</a>
            <a href="{{ route('oauth.redirect', 'github') }}" class="btn w-full text-center">Login dengan GitHub</a>
        </div>

        <p style="margin-top:32px; color:var(--muted);">
            Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
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
</script>
@endsection