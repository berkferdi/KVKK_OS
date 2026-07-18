@extends('layouts.admin')
@section('title', $policy->title.' — Politika')
@section('page_title', $policy->title)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.policies.edit', [$company, $policy]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.policies.destroy', [$company, $policy]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ $policy->category?->value ?: '—' }}</dd>
        <dt class="col-sm-3">Versiyon</dt><dd class="col-sm-9">{{ $policy->version }}</dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $policy->status->value }}</dd>
        <dt class="col-sm-3">Sorumlu</dt><dd class="col-sm-9">{{ $policy->owner_name ?: '—' }}</dd>
        <dt class="col-sm-3">Özet</dt><dd class="col-sm-9">{{ $policy->summary ?: '—' }}</dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-body">
    <pre class="mb-0" style="white-space:pre-wrap;font-family:inherit;">{{ $policy->content }}</pre>
</div></div>
@endsection
