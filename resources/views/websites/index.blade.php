@extends('layouts.admin')
@section('title', 'Web Siteleri — KVKK 360')
@section('page_title', 'Web Siteleri')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Websites\Models\Website::class, $company])
        <a href="{{ route('companies.websites.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Web Sitesi</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Ad</th><th>URL</th><th>Gizlilik</th><th>Çerez</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($websites as $website)
                <tr>
                    <td><a href="{{ route('companies.websites.show', [$company, $website]) }}">{{ $website->name }}</a></td>
                    <td><a href="{{ $website->url }}" target="_blank" rel="noopener">{{ $website->url }}</a></td>
                    <td>{{ $website->privacy_policy_published ? 'Yayında' : 'Yok' }}</td>
                    <td>{{ $website->uses_cookies ? 'Var' : 'Yok' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $website->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.websites.edit', [$company, $website]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Web sitesi kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($websites->hasPages())<div class="card-footer">{{ $websites->links() }}</div>@endif
</div>
@endsection
