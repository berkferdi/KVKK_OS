@extends('layouts.admin')
@section('title', $cookie->name.' — Çerez')
@section('page_title', $cookie->name)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.cookies.edit', [$company, $cookie]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.cookies.destroy', [$company, $cookie]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kod</dt><dd class="col-sm-9">{{ $cookie->cookie_code ?: '—' }}</dd>
        <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ $cookie->category->value }}</dd>
        <dt class="col-sm-3">Web sitesi</dt><dd class="col-sm-9">{{ $cookie->website?->name ?: '—' }}</dd>
        <dt class="col-sm-3">Sağlayıcı</dt><dd class="col-sm-9">{{ $cookie->provider ?: '—' }}</dd>
        <dt class="col-sm-3">Domain</dt><dd class="col-sm-9">{{ $cookie->domain ?: '—' }}</dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $cookie->status->value }}</dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>KVKK uyum kayıtları</strong></div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Amaç</dt><dd class="col-sm-9">{{ $cookie->purpose ?: '—' }}</dd>
        <dt class="col-sm-3">Süre</dt><dd class="col-sm-9">{{ $cookie->duration ?: '—' }} {{ $cookie->duration_days !== null ? '('.$cookie->duration_days.' gün)' : '' }}</dd>
        <dt class="col-sm-3">Üçüncü taraf</dt><dd class="col-sm-9">{{ $cookie->is_third_party ? 'Evet' : 'Hayır' }}</dd>
        <dt class="col-sm-3">Rıza gerektirir</dt><dd class="col-sm-9">{{ $cookie->requires_consent ? 'Evet' : 'Hayır' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $cookie->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
