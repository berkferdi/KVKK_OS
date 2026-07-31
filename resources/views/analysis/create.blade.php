@extends('layouts.admin')

@section('title', 'KVKK Analiz Sihirbazı — KVKK 360')
@section('page_title', 'KVKK Analiz Sihirbazı')
@section('page_subtitle', $company->trade_name)

@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="mb-3">
                    Sihirbaz, firma özelliklerini (kamera, web, çerez, personel sayısı vb.)
                    <strong>veritabanındaki kurallara</strong> göre değerlendirir ve yükümlülük listesini üretir.
                </p>
                <dl class="row small">
                    <dt class="col-sm-4">Kamera</dt><dd class="col-sm-8">{{ $company->has_camera ? 'Var' : 'Yok' }}</dd>
                    <dt class="col-sm-4">Web</dt><dd class="col-sm-8">{{ $company->has_website ? 'Var' : 'Yok' }}</dd>
                    <dt class="col-sm-4">Çerez</dt><dd class="col-sm-8">{{ $company->has_cookies ? 'Var' : 'Yok' }}</dd>
                    <dt class="col-sm-4">Personel</dt><dd class="col-sm-8">{{ $company->employee_count ?? '—' }}</dd>
                    <dt class="col-sm-4">NACE</dt><dd class="col-sm-8">{{ $company->nace_code ?: '—' }}</dd>
                </dl>
                <form method="POST" action="{{ route('companies.analysis.store', $company) }}">
                    @csrf
                    <button type="submit" class="btn text-white" style="background:#1f6f5b;">
                        Analizi Başlat
                    </button>
                    <a href="{{ route('companies.show', $company) }}" class="btn btn-outline-secondary">Geri</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Son analizler</strong></div>
            <ul class="list-group list-group-flush">
                @forelse ($recentRuns as $run)
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('analysis.show', $run) }}">{{ $run->created_at?->format('d.m.Y H:i') }}</a>
                        <span class="badge text-bg-secondary">{{ $run->status->value }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Henüz analiz yok.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
