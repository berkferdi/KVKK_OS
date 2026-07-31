@extends('layouts.admin')

@section('title', 'Analiz Sonucu — KVKK 360')
@section('page_title', 'Analiz Sonucu')
@section('page_subtitle', $run->company->trade_name)

@section('page_actions')
    @can('create', [App\Domain\Ai\Models\AiGeneration::class, $run->company])
        <form method="POST" action="{{ route('companies.analysis.ai-summary', [$run->company, $run]) }}" class="d-inline">
            @csrf
            <button class="btn btn-sm text-white" style="background:#1f6f5b;" type="submit">AI Özet</button>
        </form>
    @endcan
    <a href="{{ route('companies.analysis.create', $run->company) }}" class="btn btn-sm btn-outline-secondary">Yeniden çalıştır</a>
    <a href="{{ route('companies.show', $run->company) }}" class="btn btn-sm btn-outline-primary">Firmaya dön</a>
@endsection

@section('content')
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <div class="text-muted small">Durum</div>
            <strong>{{ $run->status->value }}</strong>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <div class="text-muted small">Eşleşen kural</div>
            <strong>{{ $run->matched_rules_count }}</strong>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <div class="text-muted small">Bulgu</div>
            <strong>{{ $run->findings_count }}</strong>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <div class="text-muted small">Tetikleyen</div>
            <strong>{{ $run->triggeredByUser?->name ?: '—' }}</strong>
        </div></div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><strong>Yükümlülük / Bulgular</strong></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
            <tr>
                <th>Tip</th>
                <th>Kod</th>
                <th>Başlık</th>
                <th>Önem</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($run->findings as $finding)
                <tr>
                    <td><code>{{ $finding->type }}</code></td>
                    <td>{{ $finding->code }}</td>
                    <td>
                        <div>{{ $finding->title }}</div>
                        @if ($finding->description)
                            <div class="small text-muted">{{ $finding->description }}</div>
                        @endif
                    </td>
                    <td><span class="badge text-bg-warning">{{ $finding->severity->value }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Bulgu yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
