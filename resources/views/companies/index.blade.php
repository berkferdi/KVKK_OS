@extends('layouts.admin')

@section('title', 'Firmalar — KVKK 360')
@section('page_title', 'Firmalar')
@section('page_subtitle', 'KVKK uyum konusu işletmeler')

@section('page_actions')
    @can('create', App\Domain\Organization\Models\Company::class)
        <a href="{{ route('companies.create') }}" class="btn btn-sm btn-kvkk text-white" style="background:#1f6f5b;">Yeni Firma</a>
    @endcan
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Unvan</th>
                    <th>Vergi No</th>
                    <th>Şehir</th>
                    <th>Durum</th>
                    <th>Bayraklar</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($companies as $company)
                    <tr>
                        <td>
                            <a href="{{ route('companies.show', $company) }}">{{ $company->trade_name }}</a>
                        </td>
                        <td>{{ $company->tax_number ?: '—' }}</td>
                        <td>{{ $company->city ?: '—' }}</td>
                        <td><span class="badge text-bg-secondary">{{ $company->status->value }}</span></td>
                        <td class="small text-muted">
                            @if($company->has_camera) Kamera @endif
                            @if($company->has_website) Web @endif
                            @if($company->has_cookies) Çerez @endif
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('companies.edit', $company) }}">Düzenle</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Henüz firma yok.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($companies->hasPages())
        <div class="card-footer">{{ $companies->links() }}</div>
    @endif
</div>
@endsection
