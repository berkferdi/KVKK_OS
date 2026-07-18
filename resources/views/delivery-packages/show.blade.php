@extends('layouts.admin')
@section('title', $package->title.' — Teslim Paketi')
@section('page_title', $package->title)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @if($package->isDownloadable())
        <a href="{{ route('companies.delivery-packages.download', [$company, $package]) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">ZIP İndir</a>
    @endif
    <form method="POST" action="{{ route('companies.delivery-packages.destroy', [$company, $package]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
    <a href="{{ route('companies.delivery-packages.index', $company) }}" class="btn btn-sm btn-outline-secondary">Liste</a>
@endsection
@section('content')
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Durum / Sürüm</dt><dd class="col-sm-9">{{ $package->status->value }} / v{{ $package->version }}</dd>
        <dt class="col-sm-3">Dosya sayısı</dt><dd class="col-sm-9">{{ $package->document_count }}</dd>
        <dt class="col-sm-3">Boyut</dt><dd class="col-sm-9">{{ $package->file_size ? number_format($package->file_size / 1024, 1).' KB' : '—' }}</dd>
        <dt class="col-sm-3">Yol</dt><dd class="col-sm-9"><code>{{ $package->file_path ?: '—' }}</code></dd>
        <dt class="col-sm-3">Üretim</dt><dd class="col-sm-9">{{ $package->generated_at?->format('d.m.Y H:i') ?: '—' }}</dd>
        @if($package->error_message)
            <dt class="col-sm-3">Hata</dt><dd class="col-sm-9 text-danger">{{ $package->error_message }}</dd>
        @endif
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Paket içeriği</strong></div>
<div class="card-body">
    @php $entries = collect($package->folder_snapshot ?? [])->reject(fn ($e) => str_ends_with($e, '/.keep')); @endphp
    @if($entries->isEmpty())
        <p class="text-muted mb-0">İçerik listesi yok.</p>
    @else
        <ul class="mb-0 small">
            @foreach($entries as $entry)
                <li><code>{{ $entry }}</code></li>
            @endforeach
        </ul>
    @endif
</div></div>
@endsection
