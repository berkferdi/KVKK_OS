@extends('layouts.admin')
@section('title', 'Veri İhlalleri — KVKK 360')
@section('page_title', 'Veri İhlalleri')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Breaches\Models\DataBreach::class, $company])
        <a href="{{ route('companies.breaches.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni İhlal</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Başlık</th><th>Önem</th><th>Tespit</th><th>Kurum bildirimi</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($breaches as $breach)
                <tr>
                    <td><a href="{{ route('companies.breaches.show', [$company, $breach]) }}">{{ $breach->title }}</a></td>
                    <td>{{ $breach->severity->value }}</td>
                    <td>{{ $breach->discovered_at?->format('d.m.Y H:i') ?: '—' }}</td>
                    <td>
                        {{ $breach->authority_notified_at?->format('d.m.Y H:i') ?: ($breach->authority_notification_due_at?->format('d.m.Y H:i') ?: '—') }}
                        @if($breach->isAuthorityNotificationOverdue())
                            <span class="badge text-bg-danger">72s aşıldı</span>
                        @endif
                    </td>
                    <td><span class="badge text-bg-secondary">{{ $breach->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.breaches.edit', [$company, $breach]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Veri ihlali kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($breaches->hasPages())<div class="card-footer">{{ $breaches->links() }}</div>@endif
</div>
@endsection
