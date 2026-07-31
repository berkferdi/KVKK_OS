@extends('layouts.admin')
@section('title', 'Personel — KVKK 360')
@section('page_title', 'Personel')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Personnel\Models\Employee::class, $company])
        <a href="{{ route('companies.personnel.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Personel</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Ad Soyad</th><th>Sicil</th><th>Departman</th><th>Şube</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($employees as $employee)
                <tr>
                    <td><a href="{{ route('companies.personnel.show', [$company, $employee]) }}">{{ $employee->fullName() }}</a></td>
                    <td>{{ $employee->employee_code ?: '—' }}</td>
                    <td>{{ $employee->department ?: '—' }}</td>
                    <td>{{ $employee->branch?->name ?: '—' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $employee->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.personnel.edit', [$company, $employee]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Personel kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($employees->hasPages())<div class="card-footer">{{ $employees->links() }}</div>@endif
</div>
@endsection
