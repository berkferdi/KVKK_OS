@extends('layouts.admin')

@section('title', 'Veri İşleme Envanteri — KVKK 360')
@section('page_title', 'Veri İşleme Envanteri')
@section('page_subtitle', $company->trade_name)

@section('page_actions')
    @can('create', [App\Domain\Inventory\Models\ProcessingActivity::class, $company])
        <a href="{{ route('companies.inventory.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Kayıt</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
            <tr>
                <th>Faaliyet</th>
                <th>Hukuki sebep</th>
                <th>Durum</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($activities as $activity)
                <tr>
                    <td>
                        <a href="{{ route('companies.inventory.show', [$company, $activity]) }}">{{ $activity->name }}</a>
                        <div class="small text-muted">{{ $activity->purpose }}</div>
                    </td>
                    <td>{{ $activity->legal_basis?->value ?: '—' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $activity->status->value }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('companies.inventory.edit', [$company, $activity]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Envanter kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($activities->hasPages())
        <div class="card-footer">{{ $activities->links() }}</div>
    @endif
</div>
@endsection
