@extends('layouts.admin')
@section('title', 'Üretilen Belgeler — KVKK 360')
@section('page_title', 'Üretilen Belgeler')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Documents\Models\GeneratedDocument::class, $company])
        <a href="{{ route('companies.generated-documents.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Belge Üret</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Başlık</th><th>Şablon</th><th>Sürüm</th><th>Durum</th><th>Tarih</th><th></th></tr></thead>
            <tbody>
            @forelse ($documents as $document)
                <tr>
                    <td><a href="{{ route('companies.generated-documents.show', [$company, $document]) }}">{{ $document->title }}</a></td>
                    <td><code>{{ $document->code }}</code></td>
                    <td>v{{ $document->version }}</td>
                    <td><span class="badge text-bg-secondary">{{ $document->status->value }}</span></td>
                    <td>{{ $document->generated_at?->format('d.m.Y H:i') ?: '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('companies.generated-documents.show', [$company, $document]) }}" class="btn btn-sm btn-outline-primary">Görüntüle</a>
                        @if($document->status->value !== 'failed')
                            <a href="{{ route('companies.generated-documents.download', [$company, $document]) }}" class="btn btn-sm btn-outline-success">Word</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Üretilen belge yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($documents->hasPages())<div class="card-footer">{{ $documents->links() }}</div>@endif
</div>
@endsection
