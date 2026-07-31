@extends('layouts.admin')
@section('title', $entry->title.' — VERBİS')
@section('page_title', $entry->title)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.verbis.entries.edit', [$company, $entry]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.verbis.entries.destroy', [$company, $entry]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kod</dt><dd class="col-sm-9">{{ $entry->code ?: '—' }}</dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $entry->status->value }}</dd>
        <dt class="col-sm-3">Envanter</dt><dd class="col-sm-9">{{ $entry->processingActivity?->name ?: '—' }}</dd>
        <dt class="col-sm-3">Hukuki sebep</dt><dd class="col-sm-9">{{ $entry->legal_basis ?: '—' }}</dd>
        <dt class="col-sm-3">Amaçlar</dt><dd class="col-sm-9">{{ $entry->purposes ?: '—' }}</dd>
        <dt class="col-sm-3">İlgili kişiler</dt><dd class="col-sm-9">{{ $entry->data_subject_categories ?: '—' }}</dd>
        <dt class="col-sm-3">Veri kategorileri</dt><dd class="col-sm-9">{{ $entry->data_categories ?: '—' }}</dd>
        <dt class="col-sm-3">Alıcılar</dt><dd class="col-sm-9">{{ $entry->recipients ?: '—' }}</dd>
        <dt class="col-sm-3">Saklama</dt><dd class="col-sm-9">{{ $entry->retention_period ?: '—' }}</dd>
        <dt class="col-sm-3">Yurt dışı aktarım</dt><dd class="col-sm-9">{{ $entry->cross_border_transfer ? 'Evet' : 'Hayır' }} {{ $entry->transfer_countries }}</dd>
        <dt class="col-sm-3">Güvenlik tedbirleri</dt><dd class="col-sm-9">{{ $entry->security_measures ?: '—' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $entry->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
