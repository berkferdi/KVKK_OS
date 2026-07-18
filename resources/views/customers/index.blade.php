@extends('layouts.admin')
@section('title', 'Müşteriler — KVKK 360')
@section('page_title', 'Müşteriler')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Customers\Models\Customer::class, $company])
        <a href="{{ route('companies.customers.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Müşteri</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Ad / Unvan</th><th>Kod</th><th>Tür</th><th>Şube</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td><a href="{{ route('companies.customers.show', [$company, $customer]) }}">{{ $customer->name }}</a></td>
                    <td>{{ $customer->customer_code ?: '—' }}</td>
                    <td>{{ $customer->customer_type->value }}</td>
                    <td>{{ $customer->branch?->name ?: '—' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $customer->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.customers.edit', [$company, $customer]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Müşteri kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($customers->hasPages())<div class="card-footer">{{ $customers->links() }}</div>@endif
</div>
@endsection
