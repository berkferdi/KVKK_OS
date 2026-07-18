@extends('layouts.admin')
@section('title', $supplier->name.' — Tedarikçi')
@section('page_title', $supplier->name)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.suppliers.edit', [$company, $supplier]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.suppliers.destroy', [$company, $supplier]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kod</dt><dd class="col-sm-9">{{ $supplier->supplier_code ?: '—' }}</dd>
        <dt class="col-sm-3">Tür</dt><dd class="col-sm-9">{{ $supplier->supplier_type->value }}</dd>
        <dt class="col-sm-3">Şube</dt><dd class="col-sm-9">{{ $supplier->branch?->name ?: '—' }}</dd>
        <dt class="col-sm-3">İlgili kişi</dt><dd class="col-sm-9">{{ $supplier->contact_person ?: '—' }}</dd>
        <dt class="col-sm-3">Vergi No</dt><dd class="col-sm-9">{{ $supplier->tax_number ?: '—' }}</dd>
        <dt class="col-sm-3">İletişim</dt><dd class="col-sm-9">{{ $supplier->email ?: '—' }} / {{ $supplier->phone ?: '—' }}</dd>
        <dt class="col-sm-3">Adres</dt><dd class="col-sm-9">{{ $supplier->address ?: '—' }} {{ $supplier->district }} / {{ $supplier->city }}</dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $supplier->status->value }}</dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>KVKK uyum kayıtları</strong></div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Sözleşme</dt>
        <dd class="col-sm-9">{{ $supplier->contract_start?->format('d.m.Y') ?: '—' }} / {{ $supplier->contract_end?->format('d.m.Y') ?: '—' }}</dd>
        <dt class="col-sm-3">Kişisel veri işler</dt><dd class="col-sm-9">{{ $supplier->processes_personal_data ? 'Evet' : 'Hayır' }}</dd>
        <dt class="col-sm-3">Aydınlatma imza</dt><dd class="col-sm-9">{{ $supplier->privacy_notice_signed_at?->format('d.m.Y') ?: '—' }}</dd>
        <dt class="col-sm-3">Veri işleme sözleşmesi</dt><dd class="col-sm-9">{{ $supplier->dpa_signed_at?->format('d.m.Y') ?: '—' }}</dd>
        <dt class="col-sm-3">Veri kategorileri</dt><dd class="col-sm-9">{{ $supplier->data_categories ?: '—' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $supplier->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
