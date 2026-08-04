@extends('layouts.admin')
@section('title', $procedure->title.' — Prosedür')
@section('page_title', $procedure->title)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.procedures.edit', [$company, $procedure]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.procedures.destroy', [$company, $procedure]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Politika</dt><dd class="col-sm-9">{{ $procedure->policyDocument?->title ?: '—' }}</dd>
        <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ $procedure->category?->value ?: '—' }}</dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $procedure->status->value }}</dd>
        <dt class="col-sm-3">Özet</dt><dd class="col-sm-9">{{ $procedure->summary ?: '—' }}</dd>
    </dl>
</div></div>
<div class="row g-3">
    <div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>İçerik</strong></div><div class="card-body"><pre class="mb-0" style="white-space:pre-wrap;font-family:inherit;">{{ $procedure->content }}</pre></div></div></div>
    <div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Adımlar</strong></div><div class="card-body"><pre class="mb-0" style="white-space:pre-wrap;font-family:inherit;">{{ $procedure->steps }}</pre></div></div></div>
</div>
@endsection
