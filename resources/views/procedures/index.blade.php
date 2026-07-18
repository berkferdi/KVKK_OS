@extends('layouts.admin')
@section('title', 'Prosedürler — KVKK 360')
@section('page_title', 'Prosedürler')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Documents\Models\ProcedureDocument::class, $company])
        <a href="{{ route('companies.procedures.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Prosedür</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Başlık</th><th>Politika</th><th>Kategori</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($procedures as $procedure)
                <tr>
                    <td><a href="{{ route('companies.procedures.show', [$company, $procedure]) }}">{{ $procedure->title }}</a></td>
                    <td>{{ $procedure->policyDocument?->title ?: '—' }}</td>
                    <td>{{ $procedure->category?->value ?: '—' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $procedure->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.procedures.edit', [$company, $procedure]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Prosedür yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($procedures->hasPages())<div class="card-footer">{{ $procedures->links() }}</div>@endif
</div>
@endsection
