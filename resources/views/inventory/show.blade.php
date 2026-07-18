@extends('layouts.admin')

@section('title', $activity->name.' — Envanter')
@section('page_title', $activity->name)
@section('page_subtitle', $company->trade_name)

@section('page_actions')
    <a href="{{ route('companies.inventory.edit', [$company, $activity]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.inventory.destroy', [$company, $activity]) }}" class="d-inline"
          onsubmit="return confirm('Kayıt silinsin mi?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Amaç</dt><dd class="col-sm-9">{{ $activity->purpose ?: '—' }}</dd>
            <dt class="col-sm-3">Hukuki sebep</dt><dd class="col-sm-9">{{ $activity->legal_basis?->value ?: '—' }}</dd>
            <dt class="col-sm-3">Veri kategorileri</dt><dd class="col-sm-9">{{ implode(', ', $activity->data_categories ?? []) ?: '—' }}</dd>
            <dt class="col-sm-3">İlgili kişiler</dt><dd class="col-sm-9">{{ implode(', ', $activity->data_subject_categories ?? []) ?: '—' }}</dd>
            <dt class="col-sm-3">Alıcılar</dt><dd class="col-sm-9">{{ implode(', ', $activity->recipients ?? []) ?: '—' }}</dd>
            <dt class="col-sm-3">Saklama</dt><dd class="col-sm-9">{{ $activity->retention_period ?: '—' }}</dd>
            <dt class="col-sm-3">Yurt dışı</dt><dd class="col-sm-9">{{ $activity->cross_border_transfer ? 'Evet' : 'Hayır' }}</dd>
            <dt class="col-sm-3">Güvenlik</dt><dd class="col-sm-9">{{ $activity->security_measures ?: '—' }}</dd>
        </dl>
    </div>
</div>
<a href="{{ route('companies.inventory.index', $company) }}" class="btn btn-outline-secondary btn-sm">Listeye dön</a>
@endsection
