@extends('layouts.admin')
@section('title', 'AI Üretimleri — KVKK 360')
@section('page_title', 'AI Engine')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Ai\Models\AiGeneration::class, $company])
        <a href="{{ route('companies.ai.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Üretim</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Amaç</th><th>Sürücü</th><th>Durum</th><th>Tarih</th><th></th></tr></thead>
            <tbody>
            @forelse ($generations as $generation)
                <tr>
                    <td><a href="{{ route('companies.ai.show', [$company, $generation]) }}">{{ $generation->purpose->value }}</a></td>
                    <td><code>{{ $generation->driver }}</code></td>
                    <td><span class="badge text-bg-secondary">{{ $generation->status->value }}</span></td>
                    <td>{{ $generation->created_at?->format('d.m.Y H:i') }}</td>
                    <td class="text-end"><a href="{{ route('companies.ai.show', [$company, $generation]) }}" class="btn btn-sm btn-outline-primary">Görüntüle</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">AI üretimi yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($generations->hasPages())<div class="card-footer">{{ $generations->links() }}</div>@endif
</div>
@endsection
