@extends('layouts.admin')
@section('title', 'AI Üret — KVKK 360')
@section('page_title', 'AI Üret')
@section('page_subtitle', $company->trade_name)
@section('content')
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.ai.store', $company) }}" id="ai-form">
    @csrf
    <div class="mb-3">
        <label class="form-label" for="purpose">Amaç *</label>
        <select name="purpose" id="purpose" class="form-select" required>
            @foreach ($purposes as $purpose)
                <option value="{{ $purpose->value }}" @selected(old('purpose') === $purpose->value)>{{ $purpose->value }}</option>
            @endforeach
        </select>
        @error('purpose')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3" id="template-field">
        <label class="form-label" for="document_template_id">Şablon (belge taslağı)</label>
        <select name="document_template_id" id="document_template_id" class="form-select">
            <option value="">Seçin…</option>
            @foreach ($templates as $template)
                <option value="{{ $template->id }}" @selected((string) old('document_template_id') === (string) $template->id)>
                    {{ $template->title }} ({{ $template->code }})
                </option>
            @endforeach
        </select>
        @error('document_template_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3" id="analysis-field">
        <label class="form-label" for="analysis_run_id">Analiz çalışması (bulgu özeti)</label>
        <select name="analysis_run_id" id="analysis_run_id" class="form-select">
            <option value="">Seçin…</option>
            @foreach ($analysisRuns as $run)
                <option value="{{ $run->id }}" @selected((string) old('analysis_run_id') === (string) $run->id)>
                    #{{ $run->id }} — {{ $run->status->value }} ({{ $run->findings_count }} bulgu)
                </option>
            @endforeach
        </select>
        @error('analysis_run_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <p class="text-muted small">Varsayılan sürücü: <code>heuristic</code> (API anahtarı gerekmez). OpenAI için <code>AI_DRIVER=openai</code> ve <code>OPENAI_API_KEY</code> kullanın. Prompt’a vergi/MERSİS/e-posta/telefon/adres gönderilmez.</p>
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Üret</button>
        <a href="{{ route('companies.ai.index', $company) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
<script>
(() => {
    const purpose = document.getElementById('purpose');
    const templateField = document.getElementById('template-field');
    const analysisField = document.getElementById('analysis-field');
    const sync = () => {
        const value = purpose.value;
        templateField.style.display = value === 'document_draft' ? '' : 'none';
        analysisField.style.display = value === 'findings_summary' ? '' : 'none';
    };
    purpose.addEventListener('change', sync);
    sync();
})();
</script>
@endsection
