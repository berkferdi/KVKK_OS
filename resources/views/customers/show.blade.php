@extends('layouts.admin')
@section('title', $customer->name.' — Müşteri')
@section('page_title', $customer->name)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.customers.edit', [$company, $customer]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.customers.destroy', [$company, $customer]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kod</dt><dd class="col-sm-9">{{ $customer->customer_code ?: '—' }}</dd>
        <dt class="col-sm-3">Tür</dt><dd class="col-sm-9">{{ $customer->customer_type->value }}</dd>
        <dt class="col-sm-3">Şube</dt><dd class="col-sm-9">{{ $customer->branch?->name ?: '—' }}</dd>
        <dt class="col-sm-3">İlgili kişi</dt><dd class="col-sm-9">{{ $customer->contact_person ?: '—' }}</dd>
        <dt class="col-sm-3">Vergi / TCKN</dt><dd class="col-sm-9">{{ $customer->tax_number ?: '—' }}</dd>
        <dt class="col-sm-3">İletişim</dt><dd class="col-sm-9">{{ $customer->email ?: '—' }} / {{ $customer->phone ?: '—' }}</dd>
        <dt class="col-sm-3">Adres</dt><dd class="col-sm-9">{{ $customer->address ?: '—' }} {{ $customer->district }} / {{ $customer->city }}</dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $customer->status->value }}</dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>KVKK uyum kayıtları</strong></div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Aydınlatma imza</dt><dd class="col-sm-9">{{ $customer->privacy_notice_signed_at?->format('d.m.Y') ?: '—' }}</dd>
        <dt class="col-sm-3">Açık rıza tarihi</dt><dd class="col-sm-9">{{ $customer->consent_obtained_at?->format('d.m.Y') ?: '—' }}</dd>
        <dt class="col-sm-3">Pazarlama izni</dt><dd class="col-sm-9">{{ $customer->marketing_consent ? 'Evet' : 'Hayır' }}</dd>
        <dt class="col-sm-3">Veri kategorileri</dt><dd class="col-sm-9">{{ $customer->data_categories ?: '—' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $customer->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
