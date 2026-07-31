@extends('layouts.admin')
@section('title', $audit->title.' — Denetim')
@section('page_title', $audit->title)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.audits.edit', [$company, $audit]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.audits.destroy', [$company, $audit]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kod</dt><dd class="col-sm-9">{{ $audit->audit_code ?: '—' }}</dd>
        <dt class="col-sm-3">Tür / Sonuç</dt><dd class="col-sm-9">{{ $audit->audit_type->value }} / {{ $audit->result->value }}</dd>
        <dt class="col-sm-3">Şube</dt><dd class="col-sm-9">{{ $audit->branch?->name ?: '—' }}</dd>
        <dt class="col-sm-3">Durum</dt>
        <dd class="col-sm-9">
            {{ $audit->status->value }}
            @if($audit->isScheduleOverdue())
                <span class="badge text-bg-warning">Plan gecikti</span>
            @endif
            @if($audit->isNextAuditOverdue())
                <span class="badge text-bg-danger">Sonraki denetim gecikti</span>
            @endif
        </dd>
        <dt class="col-sm-3">Denetçi</dt><dd class="col-sm-9">{{ $audit->auditor_name ?: '—' }}</dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Takvim ve bulgular</strong></div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Plan / Başlangıç / Bitiş</dt>
        <dd class="col-sm-9">
            {{ $audit->planned_at?->format('d.m.Y H:i') ?: '—' }} /
            {{ $audit->started_at?->format('d.m.Y H:i') ?: '—' }} /
            {{ $audit->completed_at?->format('d.m.Y H:i') ?: '—' }}
        </dd>
        <dt class="col-sm-3">Sonraki denetim</dt>
        <dd class="col-sm-9">{{ $audit->next_audit_due_at?->format('d.m.Y H:i') ?: '—' }}</dd>
        <dt class="col-sm-3">Kapsam</dt><dd class="col-sm-9">{{ $audit->scope ?: '—' }}</dd>
        <dt class="col-sm-3">Bulgular</dt><dd class="col-sm-9">{{ $audit->findings ?: '—' }}</dd>
        <dt class="col-sm-3">Öneriler</dt><dd class="col-sm-9">{{ $audit->recommendations ?: '—' }}</dd>
        <dt class="col-sm-3">Düzeltici faaliyetler</dt><dd class="col-sm-9">{{ $audit->corrective_actions ?: '—' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $audit->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
