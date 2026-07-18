@extends('layouts.admin')
@section('title', 'Teslim Paketleri — KVKK 360')
@section('page_title', 'Teslim Paketleri')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Documents\Models\DeliveryPackage::class, $company])
        <form method="POST" action="{{ route('companies.delivery-packages.store', $company) }}" class="d-inline">
            @csrf
            <button class="btn btn-sm text-white" style="background:#1f6f5b;" type="submit">ZIP Oluştur</button>
        </form>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Başlık</th><th>Sürüm</th><th>Dosya</th><th>Durum</th><th>Tarih</th><th></th></tr></thead>
            <tbody>
            @forelse ($packages as $package)
                <tr>
                    <td><a href="{{ route('companies.delivery-packages.show', [$company, $package]) }}">{{ $package->title }}</a></td>
                    <td>v{{ $package->version }}</td>
                    <td>{{ $package->document_count }} dosya</td>
                    <td><span class="badge text-bg-secondary">{{ $package->status->value }}</span></td>
                    <td>{{ $package->generated_at?->format('d.m.Y H:i') ?: '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('companies.delivery-packages.show', [$company, $package]) }}" class="btn btn-sm btn-outline-primary">Detay</a>
                        @if($package->isDownloadable())
                            <a href="{{ route('companies.delivery-packages.download', [$company, $package]) }}" class="btn btn-sm btn-outline-success">İndir</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Teslim paketi yok. Önce belge üretin, sonra ZIP oluşturun.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($packages->hasPages())<div class="card-footer">{{ $packages->links() }}</div>@endif
</div>
@endsection
