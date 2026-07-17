@extends('layouts.admin')
@section('title', $camera->name.' — Kamera')
@section('page_title', $camera->name)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.cameras.edit', [$company, $camera]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.cameras.destroy', [$company, $camera]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kod</dt><dd class="col-sm-9">{{ $camera->camera_code ?: '—' }}</dd>
        <dt class="col-sm-3">Tür</dt><dd class="col-sm-9">{{ $camera->camera_type->value }}</dd>
        <dt class="col-sm-3">Şube</dt><dd class="col-sm-9">{{ $camera->branch?->name ?: '—' }}</dd>
        <dt class="col-sm-3">Konum</dt><dd class="col-sm-9">{{ $camera->location ?: '—' }}</dd>
        <dt class="col-sm-3">Kapsama</dt><dd class="col-sm-9">{{ $camera->coverage_area ?: '—' }}</dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $camera->status->value }}</dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>KVKK uyum kayıtları</strong></div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kayıt / Ses</dt><dd class="col-sm-9">{{ $camera->is_recording ? 'Evet' : 'Hayır' }} / {{ $camera->records_audio ? 'Evet' : 'Hayır' }}</dd>
        <dt class="col-sm-3">Saklama süresi</dt><dd class="col-sm-9">{{ $camera->retention_days ? $camera->retention_days.' gün' : '—' }}</dd>
        <dt class="col-sm-3">Depolama</dt><dd class="col-sm-9">{{ $camera->storage_location ?: '—' }}</dd>
        <dt class="col-sm-3">Aydınlatma tabelası</dt><dd class="col-sm-9">{{ $camera->notice_posted ? 'Var' : 'Yok' }} {{ $camera->notice_posted_at?->format('(d.m.Y)') }}</dd>
        <dt class="col-sm-3">Kurulum</dt><dd class="col-sm-9">{{ $camera->installed_at?->format('d.m.Y') ?: '—' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $camera->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
