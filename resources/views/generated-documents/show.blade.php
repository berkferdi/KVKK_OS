@extends('layouts.admin')
@section('title', $document->title.' — Üretilen Belge')
@section('page_title', $document->title)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <form method="POST" action="{{ route('companies.generated-documents.destroy', [$company, $document]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
    <a href="{{ route('companies.generated-documents.index', $company) }}" class="btn btn-sm btn-outline-secondary">Liste</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Şablon</dt><dd class="col-sm-9"><code>{{ $document->code }}</code> — {{ $document->template?->title }}</dd>
        <dt class="col-sm-3">Durum / Sürüm</dt><dd class="col-sm-9">{{ $document->status->value }} / v{{ $document->version }}</dd>
        <dt class="col-sm-3">Üretim</dt><dd class="col-sm-9">{{ $document->generated_at?->format('d.m.Y H:i') ?: '—' }}</dd>
        @if (! empty($document->missing_placeholders))
            <dt class="col-sm-3">Eksik alanlar</dt>
            <dd class="col-sm-9">
                @foreach ($document->missing_placeholders as $key)
                    <code class="me-1">{{ $key }}</code>
                @endforeach
            </dd>
        @endif
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Önizleme</strong></div>
<div class="card-body">
    @if ($document->rendered_content)
        <pre class="mb-0" style="white-space: pre-wrap;">{{ $document->rendered_content }}</pre>
    @else
        <p class="text-muted mb-0">İçerik üretilemedi.</p>
    @endif
</div></div>
@endsection
