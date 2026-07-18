@extends('layouts.admin')
@section('title', 'Belge Üret — KVKK 360')
@section('page_title', 'Belge Üret')
@section('page_subtitle', $company->trade_name)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.generated-documents.store', $company) }}">
    @csrf
    <div class="mb-3">
        <label class="form-label" for="document_template_id">Şablon *</label>
        <select name="document_template_id" id="document_template_id" class="form-select" required>
            <option value="">Seçin…</option>
            @foreach ($templates as $template)
                <option value="{{ $template->id }}" @selected((string) old('document_template_id') === (string) $template->id)>
                    {{ $template->title }} ({{ $template->code }})
                </option>
            @endforeach
        </select>
        @error('document_template_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <p class="text-muted small mb-0">Firma alanlarından <code>{{'{{'}}firma_unvani{{'}}'}}</code> vb. placeholder’lar doldurulur. Eksik alan varsa üretim başarısız kaydedilir.</p>
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Üret</button>
        <a href="{{ route('companies.generated-documents.index', $company) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
