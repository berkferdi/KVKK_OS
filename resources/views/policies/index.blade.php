@extends('layouts.admin')
@section('title', 'Politikalar — KVKK 360')
@section('page_title', 'Politikalar')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Documents\Models\PolicyDocument::class, $company])
        <a href="{{ route('companies.policies.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Politika</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Başlık</th><th>Kategori</th><th>Versiyon</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($policies as $policy)
                <tr>
                    <td><a href="{{ route('companies.policies.show', [$company, $policy]) }}">{{ $policy->title }}</a></td>
                    <td>{{ $policy->category?->value ?: '—' }}</td>
                    <td>{{ $policy->version }}</td>
                    <td><span class="badge text-bg-secondary">{{ $policy->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.policies.edit', [$company, $policy]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Politika yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($policies->hasPages())<div class="card-footer">{{ $policies->links() }}</div>@endif
</div>
@endsection
