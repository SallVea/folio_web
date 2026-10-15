@extends('layouts.dashboard')

@section('title', 'Notifikasi')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl font-bold mb-1">Notifikasi</h1>
            <p class="text-sm text-muted">Pengingat dan pembaruan aktivitas akunmu.</p>
        </div>
        @if (auth()->user()->unreadNotifications->isNotEmpty())
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button type="submit" class="text-xs text-muted hover:text-[#232323] underline">
                    Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    @if ($notifications->isEmpty())
        <div class="border border-border px-5 py-10 text-center text-sm text-muted">
            Belum ada notifikasi.
        </div>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $unread = is_null($notification->read_at);
                @endphp
                <div class="border border-border px-5 py-4 flex items-start gap-4 {{ $unread ? 'bg-surface' : '' }}">
                    <span class="mt-1 text-xs">{{ $unread ? '●' : '○' }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ $data['title'] ?? 'Notifikasi' }}</p>
                        <p class="text-sm text-muted mt-1">{{ $data['message'] ?? '' }}</p>
                        <p class="text-xs text-muted mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit" class="text-xs text-muted hover:text-[#232323] underline whitespace-nowrap">
                            {{ $unread ? 'Buka' : 'Lihat' }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @endif
@endsection
