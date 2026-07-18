@extends('layouts.admin')
@section('title', $application->applicant_name.' — Başvuru')
@section('page_title', $application->applicant_name)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.applications.edit', [$company, $application]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.applications.destroy', [$company, $application]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kod</dt><dd class="col-sm-9">{{ $application->application_code ?: '—' }}</dd>
        <dt class="col-sm-3">Talep türü</dt><dd class="col-sm-9">{{ $application->request_type->value }}</dd>
        <dt class="col-sm-3">Kanal</dt><dd class="col-sm-9">{{ $application->channel->value }}</dd>
        <dt class="col-sm-3">Şube</dt><dd class="col-sm-9">{{ $application->branch?->name ?: '—' }}</dd>
        <dt class="col-sm-3">İletişim</dt><dd class="col-sm-9">{{ $application->applicant_email ?: '—' }} / {{ $application->applicant_phone ?: '—' }}</dd>
        <dt class="col-sm-3">Durum</dt>
        <dd class="col-sm-9">
            {{ $application->status->value }}
            @if($application->isOverdue())
                <span class="badge text-bg-danger">Gecikmiş</span>
            @endif
        </dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Süreç ve yanıt</strong></div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Alınma / Son tarih / Yanıt</dt>
        <dd class="col-sm-9">
            {{ $application->received_at?->format('d.m.Y') ?: '—' }} /
            {{ $application->due_at?->format('d.m.Y') ?: '—' }} /
            {{ $application->responded_at?->format('d.m.Y') ?: '—' }}
        </dd>
        <dt class="col-sm-3">Kimlik doğrulama</dt><dd class="col-sm-9">{{ $application->identity_verified ? 'Evet' : 'Hayır' }}</dd>
        <dt class="col-sm-3">Sorumlu</dt><dd class="col-sm-9">{{ $application->assigned_to_name ?: '—' }}</dd>
        <dt class="col-sm-3">Talep özeti</dt><dd class="col-sm-9">{{ $application->request_summary ?: '—' }}</dd>
        <dt class="col-sm-3">Yanıt özeti</dt><dd class="col-sm-9">{{ $application->response_summary ?: '—' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $application->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
