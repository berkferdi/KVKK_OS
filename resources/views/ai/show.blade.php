@extends('layouts.admin')
@section('title', 'AI Üretim — KVKK 360')
@section('page_title', 'AI Üretim')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.ai.index', $company) }}" class="btn btn-sm btn-outline-secondary">Liste</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Amaç</dt><dd class="col-sm-9"><code>{{ $generation->purpose->value }}</code></dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $generation->status->value }}</dd>
        <dt class="col-sm-3">Sürücü / Model</dt><dd class="col-sm-9">{{ $generation->driver }} / {{ $generation->model ?: '—' }}</dd>
        <dt class="col-sm-3">Şablon</dt><dd class="col-sm-9">{{ $generation->template?->title ?: '—' }}</dd>
        <dt class="col-sm-3">Analiz</dt>
        <dd class="col-sm-9">
            @if($generation->analysisRun)
                <a href="{{ route('analysis.show', $generation->analysisRun) }}">#{{ $generation->analysisRun->id }}</a>
            @else
                —
            @endif
        </dd>
        @if($generation->error_message)
            <dt class="col-sm-3">Hata</dt><dd class="col-sm-9 text-danger">{{ $generation->error_message }}</dd>
        @endif
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Çıktı</strong></div>
<div class="card-body">
    @if($generation->output_text)
        <pre class="mb-0" style="white-space: pre-wrap;">{{ $generation->output_text }}</pre>
    @else
        <p class="text-muted mb-0">Çıktı yok.</p>
    @endif
</div></div>
@endsection
