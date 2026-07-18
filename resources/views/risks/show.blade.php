@extends('layouts.admin')

@section('title', $risk->title.' — Risk')
@section('page_title', $risk->title)
@section('page_subtitle', $company->trade_name)

@section('page_actions')
    <a href="{{ route('companies.risks.edit', [$company, $risk]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.risks.destroy', [$company, $risk]) }}" class="d-inline"
          onsubmit="return confirm('Risk silinsin mi?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection

@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small">Skor</div><strong>{{ $risk->score }}</strong>
    </div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small">Seviye</div><strong>{{ $risk->risk_level->value }}</strong>
    </div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small">Olasılık × Etki</div><strong>{{ $risk->likelihood }} × {{ $risk->impact }}</strong>
    </div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small">Durum</div><strong>{{ $risk->status->value }}</strong>
    </div></div></div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Envanter</dt><dd class="col-sm-9">{{ $risk->processingActivity?->name ?: '—' }}</dd>
            <dt class="col-sm-3">Varlık</dt><dd class="col-sm-9">{{ $risk->asset_type ?: '—' }}</dd>
            <dt class="col-sm-3">Tehdit</dt><dd class="col-sm-9">{{ $risk->threat ?: '—' }}</dd>
            <dt class="col-sm-3">Zafiyet</dt><dd class="col-sm-9">{{ $risk->vulnerability ?: '—' }}</dd>
            <dt class="col-sm-3">Kontroller</dt><dd class="col-sm-9">{{ $risk->existing_controls ?: '—' }}</dd>
            <dt class="col-sm-3">Azaltım</dt><dd class="col-sm-9">{{ $risk->mitigation_plan ?: '—' }}</dd>
            <dt class="col-sm-3">Sorumlu</dt><dd class="col-sm-9">{{ $risk->owner_name ?: '—' }}</dd>
        </dl>
    </div>
</div>
@endsection
