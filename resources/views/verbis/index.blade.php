@extends('layouts.admin')
@section('title', 'VERBİS — KVKK 360')
@section('page_title', 'VERBİS')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Verbis\Models\VerbisEntry::class, $company])
        <a href="{{ route('companies.verbis.entries.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Kayıt</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Sicil bilgileri</strong>
        @can('updateRegistration', $registration)
            <a href="{{ route('companies.verbis.registration.edit', $company) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
        @endcan
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Durum</dt><dd class="col-sm-9"><span class="badge text-bg-secondary">{{ $registration->status->value }}</span></dd>
            <dt class="col-sm-3">Sicil No</dt><dd class="col-sm-9">{{ $registration->registration_number ?: '—' }}</dd>
            <dt class="col-sm-3">Kayıt tarihi</dt><dd class="col-sm-9">{{ $registration->registered_at?->format('d.m.Y') ?: '—' }}</dd>
            <dt class="col-sm-3">İrtibat</dt><dd class="col-sm-9">{{ $registration->contact_name ?: '—' }} / {{ $registration->contact_email ?: '—' }}</dd>
            <dt class="col-sm-3">Muafiyet</dt><dd class="col-sm-9">{{ $registration->is_exempt ? 'Evet' : 'Hayır' }}</dd>
        </dl>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><strong>VERBİS kayıt kalemleri</strong></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Başlık</th><th>Kod</th><th>Envanter</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($entries as $entry)
                <tr>
                    <td><a href="{{ route('companies.verbis.entries.show', [$company, $entry]) }}">{{ $entry->title }}</a></td>
                    <td>{{ $entry->code ?: '—' }}</td>
                    <td>{{ $entry->processingActivity?->name ?: '—' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $entry->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.verbis.entries.edit', [$company, $entry]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">VERBİS kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($entries->hasPages())<div class="card-footer">{{ $entries->links() }}</div>@endif
</div>
@endsection
