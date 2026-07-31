@extends('layouts.admin')
@section('title', $template->title.' — Şablon')
@section('page_title', $template->title)
@section('page_subtitle', $template->code)
@section('page_actions')
    <a href="{{ route('document-templates.edit', $template) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('document-templates.destroy', $template) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ $template->category->label() }}</dd>
        <dt class="col-sm-3">Doküman No</dt><dd class="col-sm-9">{{ $template->document_number ?: '—' }}</dd>
        <dt class="col-sm-3">Versiyon / Revizyon</dt><dd class="col-sm-9">v{{ $template->version }} / {{ $template->revision_number ?: '00' }}</dd>
        <dt class="col-sm-3">Yürürlük</dt><dd class="col-sm-9">{{ $template->document_status ?: 'effective' }}</dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $template->is_active ? 'aktif' : 'pasif' }}</dd>
        <dt class="col-sm-3">Açıklama</dt><dd class="col-sm-9">{{ $template->description ?: '—' }}</dd>
        <dt class="col-sm-3">Placeholder’lar</dt>
        <dd class="col-sm-9">
            @forelse ($template->placeholderKeys() as $key)
                <code class="me-1">{{ '{'.'{'.$key.'}'.'}' }}</code>
            @empty
                —
            @endforelse
        </dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Gövde Önizleme</strong></div>
<div class="card-body">
    @if (($template->body_format ?? 'html') === 'html')
        <div class="border rounded p-3 bg-white">{!! $template->body !!}</div>
    @else
        <pre class="mb-0" style="white-space: pre-wrap;">{{ $template->body }}</pre>
    @endif
</div></div>
@endsection
