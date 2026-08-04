@extends('layouts.admin')
@section('title', 'Bildirimler — KVKK 360')
@section('page_title', 'Bildirimler')
@section('page_subtitle', $unreadCount.' okunmamış')

@section('page_actions')
    @if ($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.read-all') }}" class="d-inline">
            @csrf
            <button class="btn btn-sm btn-outline-secondary" type="submit">Tümünü okundu işaretle</button>
        </form>
    @endif
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="list-group list-group-flush">
        @forelse ($notifications as $notification)
            @php $data = $notification->data; @endphp
            <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="fw-semibold">{{ $data['title'] ?? 'Bildirim' }}</div>
                        <div class="text-muted small">{{ $data['body'] ?? '' }}</div>
                        <div class="text-muted small mt-1">{{ $notification->created_at?->diffForHumans() }}</div>
                    </div>
                    <div class="text-nowrap">
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                            @csrf
                            <button class="btn btn-sm text-white" style="background:#1f6f5b;" type="submit">
                                {{ $notification->read_at ? 'Aç' : 'Oku' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="list-group-item text-muted text-center py-5">Bildirim yok.</div>
        @endforelse
    </div>
    @if ($notifications->hasPages())
        <div class="card-footer bg-white">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
