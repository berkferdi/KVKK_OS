@extends('layouts.admin')
@section('title', 'Ziyaretçiler — KVKK 360')
@section('page_title', 'Ziyaretçiler')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Visitors\Models\Visitor::class, $company])
        <a href="{{ route('companies.visitors.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Ziyaretçi</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Ad Soyad</th><th>Kurum</th><th>Giriş</th><th>Şube</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($visitors as $visitor)
                <tr>
                    <td><a href="{{ route('companies.visitors.show', [$company, $visitor]) }}">{{ $visitor->fullName() }}</a></td>
                    <td>{{ $visitor->organization ?: '—' }}</td>
                    <td>{{ $visitor->visited_at?->format('d.m.Y H:i') ?: '—' }}</td>
                    <td>{{ $visitor->branch?->name ?: '—' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $visitor->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.visitors.edit', [$company, $visitor]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Ziyaretçi kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($visitors->hasPages())<div class="card-footer">{{ $visitors->links() }}</div>@endif
</div>
@endsection
