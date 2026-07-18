@extends('layouts.admin')
@section('title', 'Denetimler — KVKK 360')
@section('page_title', 'Denetimler')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Audits\Models\ComplianceAudit::class, $company])
        <a href="{{ route('companies.audits.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Denetim</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Başlık</th><th>Tür</th><th>Plan</th><th>Sonraki</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($audits as $audit)
                <tr>
                    <td><a href="{{ route('companies.audits.show', [$company, $audit]) }}">{{ $audit->title }}</a></td>
                    <td>{{ $audit->audit_type->value }}</td>
                    <td>
                        {{ $audit->planned_at?->format('d.m.Y H:i') ?: '—' }}
                        @if($audit->isScheduleOverdue())
                            <span class="badge text-bg-warning">plan gecikti</span>
                        @endif
                    </td>
                    <td>
                        {{ $audit->next_audit_due_at?->format('d.m.Y') ?: '—' }}
                        @if($audit->isNextAuditOverdue())
                            <span class="badge text-bg-danger">vade geçti</span>
                        @endif
                    </td>
                    <td><span class="badge text-bg-secondary">{{ $audit->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.audits.edit', [$company, $audit]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Denetim kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($audits->hasPages())<div class="card-footer">{{ $audits->links() }}</div>@endif
</div>
@endsection
