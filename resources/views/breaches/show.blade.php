@extends('layouts.admin')
@section('title', $breach->title.' — Veri İhlali')
@section('page_title', $breach->title)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.breaches.edit', [$company, $breach]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.breaches.destroy', [$company, $breach]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kod</dt><dd class="col-sm-9">{{ $breach->breach_code ?: '—' }}</dd>
        <dt class="col-sm-3">Tür / Önem</dt><dd class="col-sm-9">{{ $breach->breach_type->value }} / {{ $breach->severity->value }}</dd>
        <dt class="col-sm-3">Şube</dt><dd class="col-sm-9">{{ $breach->branch?->name ?: '—' }}</dd>
        <dt class="col-sm-3">Durum</dt>
        <dd class="col-sm-9">
            {{ $breach->status->value }}
            @if($breach->isAuthorityNotificationOverdue())
                <span class="badge text-bg-danger">Kurum bildirimi gecikti</span>
            @endif
        </dd>
        <dt class="col-sm-3">Etkilenen kişi</dt><dd class="col-sm-9">{{ $breach->affected_subjects_count ?? '—' }}</dd>
        <dt class="col-sm-3">Veri kategorileri</dt><dd class="col-sm-9">{{ $breach->data_categories ?: '—' }}</dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Bildirim ve tedbirler</strong></div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Tespit / Oluşma</dt>
        <dd class="col-sm-9">{{ $breach->discovered_at?->format('d.m.Y H:i') ?: '—' }} / {{ $breach->occurred_at?->format('d.m.Y H:i') ?: '—' }}</dd>
        <dt class="col-sm-3">Kurum bildirim (son / yapıldı)</dt>
        <dd class="col-sm-9">{{ $breach->authority_notification_due_at?->format('d.m.Y H:i') ?: '—' }} / {{ $breach->authority_notified_at?->format('d.m.Y H:i') ?: '—' }}</dd>
        <dt class="col-sm-3">İlgili kişi bildirimi</dt>
        <dd class="col-sm-9">{{ $breach->subjects_notification_required ? 'Gerekli' : 'Gerekli değil' }} — {{ $breach->subjects_notified_at?->format('d.m.Y H:i') ?: 'henüz yok' }}</dd>
        <dt class="col-sm-3">Açıklama</dt><dd class="col-sm-9">{{ $breach->description ?: '—' }}</dd>
        <dt class="col-sm-3">Sonuçlar</dt><dd class="col-sm-9">{{ $breach->consequences ?: '—' }}</dd>
        <dt class="col-sm-3">Tedbirler</dt><dd class="col-sm-9">{{ $breach->measures_taken ?: '—' }}</dd>
        <dt class="col-sm-3">Kök neden</dt><dd class="col-sm-9">{{ $breach->root_cause ?: '—' }}</dd>
        <dt class="col-sm-3">Sorumlu</dt><dd class="col-sm-9">{{ $breach->assigned_to_name ?: '—' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $breach->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
