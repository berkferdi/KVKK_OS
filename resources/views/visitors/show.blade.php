@extends('layouts.admin')
@section('title', $visitor->fullName().' — Ziyaretçi')
@section('page_title', $visitor->fullName())
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.visitors.edit', [$company, $visitor]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.visitors.destroy', [$company, $visitor]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kod</dt><dd class="col-sm-9">{{ $visitor->visitor_code ?: '—' }}</dd>
        <dt class="col-sm-3">Kurum</dt><dd class="col-sm-9">{{ $visitor->organization ?: '—' }}</dd>
        <dt class="col-sm-3">Şube</dt><dd class="col-sm-9">{{ $visitor->branch?->name ?: '—' }}</dd>
        <dt class="col-sm-3">Görüşülecek</dt><dd class="col-sm-9">{{ $visitor->host_name ?: '—' }}</dd>
        <dt class="col-sm-3">Amaç</dt><dd class="col-sm-9">{{ $visitor->purpose ?: '—' }}</dd>
        <dt class="col-sm-3">İletişim</dt><dd class="col-sm-9">{{ $visitor->email ?: '—' }} / {{ $visitor->phone ?: '—' }}</dd>
        <dt class="col-sm-3">Giriş / Çıkış</dt>
        <dd class="col-sm-9">{{ $visitor->visited_at?->format('d.m.Y H:i') ?: '—' }} / {{ $visitor->left_at?->format('d.m.Y H:i') ?: '—' }}</dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $visitor->status->value }}</dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>KVKK uyum kayıtları</strong></div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Aydınlatma imza</dt><dd class="col-sm-9">{{ $visitor->privacy_notice_signed_at?->format('d.m.Y') ?: '—' }}</dd>
        <dt class="col-sm-3">Kart verildi</dt><dd class="col-sm-9">{{ $visitor->badge_issued ? 'Evet' : 'Hayır' }}</dd>
        <dt class="col-sm-3">Fotoğraf alındı</dt><dd class="col-sm-9">{{ $visitor->photo_captured ? 'Evet' : 'Hayır' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $visitor->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
