@extends('layouts.admin')

@section('title', 'Risk Analizi — KVKK 360')
@section('page_title', 'Risk Analizi')
@section('page_subtitle', $company->trade_name)

@section('page_actions')
    @can('create', [App\Domain\Risk\Models\RiskAssessment::class, $company])
        <a href="{{ route('companies.risks.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Risk</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
            <tr>
                <th>Başlık</th>
                <th>Skor</th>
                <th>Seviye</th>
                <th>Durum</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($risks as $risk)
                <tr>
                    <td>
                        <a href="{{ route('companies.risks.show', [$company, $risk]) }}">{{ $risk->title }}</a>
                        <div class="small text-muted">{{ $risk->processingActivity?->name }}</div>
                    </td>
                    <td>{{ $risk->score }} <span class="text-muted small">({{ $risk->likelihood }}×{{ $risk->impact }})</span></td>
                    <td><span class="badge text-bg-warning">{{ $risk->risk_level->value }}</span></td>
                    <td>{{ $risk->status->value }}</td>
                    <td class="text-end">
                        <a href="{{ route('companies.risks.edit', [$company, $risk]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Risk kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($risks->hasPages())
        <div class="card-footer">{{ $risks->links() }}</div>
    @endif
</div>
@endsection
