@extends('layouts.admin')

@section('title', $company->trade_name.' — KVKK 360')
@section('page_title', $company->trade_name)
@section('page_subtitle', $company->title)

@section('page_actions')
    <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.destroy', $company) }}" class="d-inline"
          onsubmit="return confirm('Firma soft-delete edilecek. Devam?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Vergi No</dt><dd class="col-sm-8">{{ $company->tax_number ?: '—' }}</dd>
                    <dt class="col-sm-4">Vergi Dairesi</dt><dd class="col-sm-8">{{ $company->tax_office ?: '—' }}</dd>
                    <dt class="col-sm-4">MERSİS</dt><dd class="col-sm-8">{{ $company->mersis_number ?: '—' }}</dd>
                    <dt class="col-sm-4">NACE</dt><dd class="col-sm-8">{{ $company->nace_code ?: '—' }}</dd>
                    <dt class="col-sm-4">İletişim</dt><dd class="col-sm-8">{{ $company->email }} / {{ $company->phone }}</dd>
                    <dt class="col-sm-4">Adres</dt><dd class="col-sm-8">{{ $company->address }} {{ $company->district }} / {{ $company->city }}</dd>
                    <dt class="col-sm-4">Yetkili</dt><dd class="col-sm-8">{{ $company->authorized_person }} ({{ $company->authorized_title }})</dd>
                    <dt class="col-sm-4">Faaliyet</dt><dd class="col-sm-8">{{ $company->activity_summary ?: '—' }}</dd>
                </dl>
                <div class="mt-3 d-flex flex-wrap gap-2">
                    @can('create', [App\Domain\Compliance\Models\AnalysisRun::class, $company])
                        <a href="{{ route('companies.analysis.create', $company) }}" class="btn text-white" style="background:#1f6f5b;">
                            KVKK Analiz Sihirbazı
                        </a>
                    @endcan
                    @can('viewAny', App\Domain\Inventory\Models\ProcessingActivity::class)
                        <a href="{{ route('companies.inventory.index', $company) }}" class="btn btn-outline-primary">Envanter</a>
                    @endcan
                    @can('viewAny', App\Domain\Risk\Models\RiskAssessment::class)
                        <a href="{{ route('companies.risks.index', $company) }}" class="btn btn-outline-primary">Risk Analizi</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="mb-2"><strong>Durum:</strong> {{ $company->status->value }}</div>
                <div class="mb-2"><strong>Personel:</strong> {{ $company->employee_count ?? '—' }}</div>
                <div class="mb-2"><strong>Kamera:</strong> {{ $company->has_camera ? 'Evet' : 'Hayır' }}</div>
                <div class="mb-2"><strong>Web:</strong> {{ $company->has_website ? 'Evet' : 'Hayır' }}</div>
                <div><strong>Çerez:</strong> {{ $company->has_cookies ? 'Evet' : 'Hayır' }}</div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Şubeler</strong>
                @can('create', App\Domain\Organization\Models\Branch::class)
                    <a href="{{ route('companies.branches.create', $company) }}" class="btn btn-sm btn-outline-success">Ekle</a>
                @endcan
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($company->branches as $branch)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $branch->name }}
                            @if($branch->is_hq)<span class="badge text-bg-success">Merkez</span>@endif
                            <span class="text-muted small d-block">{{ $branch->city }}</span>
                        </span>
                        <span class="d-flex gap-1">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('companies.branches.edit', [$company, $branch]) }}">Düzenle</a>
                            <form method="POST" action="{{ route('companies.branches.destroy', [$company, $branch]) }}"
                                  onsubmit="return confirm('Şube silinsin mi?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
                            </form>
                        </span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Henüz şube yok.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
