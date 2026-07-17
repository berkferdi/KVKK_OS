@extends('layouts.admin')
@section('title', 'Tedarikçiler — KVKK 360')
@section('page_title', 'Tedarikçiler')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Suppliers\Models\Supplier::class, $company])
        <a href="{{ route('companies.suppliers.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Tedarikçi</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Unvan</th><th>Kod</th><th>Tür</th><th>KVK veri</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($suppliers as $supplier)
                <tr>
                    <td><a href="{{ route('companies.suppliers.show', [$company, $supplier]) }}">{{ $supplier->name }}</a></td>
                    <td>{{ $supplier->supplier_code ?: '—' }}</td>
                    <td>{{ $supplier->supplier_type->value }}</td>
                    <td>{{ $supplier->processes_personal_data ? 'Evet' : 'Hayır' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $supplier->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.suppliers.edit', [$company, $supplier]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Tedarikçi kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($suppliers->hasPages())<div class="card-footer">{{ $suppliers->links() }}</div>@endif
</div>
@endsection
