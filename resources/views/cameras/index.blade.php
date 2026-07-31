@extends('layouts.admin')
@section('title', 'Kameralar — KVKK 360')
@section('page_title', 'Kameralar')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Cameras\Models\Camera::class, $company])
        <a href="{{ route('companies.cameras.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Kamera</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Ad</th><th>Konum</th><th>Saklama</th><th>Aydınlatma</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($cameras as $camera)
                <tr>
                    <td><a href="{{ route('companies.cameras.show', [$company, $camera]) }}">{{ $camera->name }}</a></td>
                    <td>{{ $camera->location ?: '—' }}</td>
                    <td>{{ $camera->retention_days ? $camera->retention_days.' gün' : '—' }}</td>
                    <td>{{ $camera->notice_posted ? 'Var' : 'Yok' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $camera->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.cameras.edit', [$company, $camera]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Kamera kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($cameras->hasPages())<div class="card-footer">{{ $cameras->links() }}</div>@endif
</div>
@endsection
