@extends('layouts.admin')
@section('title', 'Başvurular — KVKK 360')
@section('page_title', 'İlgili Kişi Başvuruları')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Applications\Models\DataSubjectApplication::class, $company])
        <a href="{{ route('companies.applications.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Başvuru</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Başvuran</th><th>Tür</th><th>Alınma</th><th>Son tarih</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($applications as $application)
                <tr>
                    <td><a href="{{ route('companies.applications.show', [$company, $application]) }}">{{ $application->applicant_name }}</a></td>
                    <td>{{ $application->request_type->value }}</td>
                    <td>{{ $application->received_at?->format('d.m.Y') ?: '—' }}</td>
                    <td>
                        {{ $application->due_at?->format('d.m.Y') ?: '—' }}
                        @if($application->isOverdue())
                            <span class="badge text-bg-danger">Gecikmiş</span>
                        @endif
                    </td>
                    <td><span class="badge text-bg-secondary">{{ $application->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.applications.edit', [$company, $application]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Başvuru kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($applications->hasPages())<div class="card-footer">{{ $applications->links() }}</div>@endif
</div>
@endsection
