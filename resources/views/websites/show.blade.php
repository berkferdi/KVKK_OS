@extends('layouts.admin')
@section('title', $website->name.' — Web Sitesi')
@section('page_title', $website->name)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.websites.edit', [$company, $website]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.websites.destroy', [$company, $website]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kod</dt><dd class="col-sm-9">{{ $website->website_code ?: '—' }}</dd>
        <dt class="col-sm-3">URL</dt><dd class="col-sm-9"><a href="{{ $website->url }}" target="_blank" rel="noopener">{{ $website->url }}</a></dd>
        <dt class="col-sm-3">Platform / Hosting</dt><dd class="col-sm-9">{{ $website->platform ?: '—' }} / {{ $website->hosting_provider ?: '—' }}</dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $website->status->value }}</dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>KVKK uyum kayıtları</strong></div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">SSL</dt><dd class="col-sm-9">{{ $website->ssl_enabled ? 'Evet' : 'Hayır' }}</dd>
        <dt class="col-sm-3">Gizlilik politikası</dt>
        <dd class="col-sm-9">
            {{ $website->privacy_policy_published ? 'Yayında' : 'Yok' }}
            @if($website->privacy_policy_url)
                — <a href="{{ $website->privacy_policy_url }}" target="_blank" rel="noopener">{{ $website->privacy_policy_url }}</a>
            @endif
            {{ $website->privacy_policy_published_at?->format('(d.m.Y)') }}
        </dd>
        <dt class="col-sm-3">Çerez</dt><dd class="col-sm-9">{{ $website->uses_cookies ? 'Var' : 'Yok' }}</dd>
        <dt class="col-sm-3">Özellikler</dt>
        <dd class="col-sm-9">
            İletişim: {{ $website->has_contact_form ? 'Evet' : 'Hayır' }} ·
            Bülten: {{ $website->has_newsletter ? 'Evet' : 'Hayır' }} ·
            Üye: {{ $website->has_user_accounts ? 'Evet' : 'Hayır' }} ·
            Ödeme: {{ $website->has_payment ? 'Evet' : 'Hayır' }}
        </dd>
        <dt class="col-sm-3">Toplanan veri</dt><dd class="col-sm-9">{{ $website->data_collected ?: '—' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $website->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
