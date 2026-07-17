@extends('layouts.admin')
@section('title', 'Çerezler — KVKK 360')
@section('page_title', 'Çerez Envanteri')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Cookies\Models\SiteCookie::class, $company])
        <a href="{{ route('companies.cookies.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Çerez</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Ad</th><th>Kategori</th><th>Web sitesi</th><th>Rıza</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($cookies as $cookie)
                <tr>
                    <td><a href="{{ route('companies.cookies.show', [$company, $cookie]) }}">{{ $cookie->name }}</a></td>
                    <td>{{ $cookie->category->value }}</td>
                    <td>{{ $cookie->website?->name ?: '—' }}</td>
                    <td>{{ $cookie->requires_consent ? 'Evet' : 'Hayır' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $cookie->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.cookies.edit', [$company, $cookie]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Çerez kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($cookies->hasPages())<div class="card-footer">{{ $cookies->links() }}</div>@endif
</div>
@endsection
