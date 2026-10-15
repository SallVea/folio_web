@extends('layouts.dashboard')

@section('title', 'Audit Log')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <h1 class="font-display text-3xl font-700">Audit Log</h1>
    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-surface2 text-sm rounded-lg">← Kembali</a>
</div>

<div class="bg-surface border border-border rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="text-muted">
                <tr>
                    <th class="pb-3 font-medium">Waktu</th>
                    <th class="pb-3 font-medium">Aktor</th>
                    <th class="pb-3 font-medium">Aksi</th>
                    <th class="pb-3 font-medium">IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr class="border-t border-border">
                        <td class="py-3">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="py-3">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="py-3">{{ $log->action }}</td>
                        <td class="py-3 text-muted">{{ $log->ip }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $logs->links() }}</div>
</div>
@endsection