@extends('layouts.public')
@section('title','Verifikasi Email')
@section('content')
<div class="max-w-md mx-auto py-20 text-center">
    <h1 class="wordmark mb-4">Verifikasi Email</h1>
    <p class="mb-6">Kami mengirim link verifikasi ke email Anda. Klik link tersebut untuk melanjutkan.</p>
    @if (session('status') === 'verification-link-sent')
        <p class="text-green-700 mb-4">Link verifikasi baru telah dikirim.</p>
    @endif
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="btn" type="submit">Kirim ulang link</button>
    </form>
</div>
@endsection