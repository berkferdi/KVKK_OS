@extends('layouts.admin')
@section('title', $employee->fullName().' — Personel')
@section('page_title', $employee->fullName())
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.personnel.edit', [$company, $employee]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.personnel.destroy', [$company, $employee]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Sicil</dt><dd class="col-sm-9">{{ $employee->employee_code ?: '—' }}</dd>
        <dt class="col-sm-3">Şube</dt><dd class="col-sm-9">{{ $employee->branch?->name ?: '—' }}</dd>
        <dt class="col-sm-3">Departman / Unvan</dt><dd class="col-sm-9">{{ $employee->department ?: '—' }} / {{ $employee->job_title ?: '—' }}</dd>
        <dt class="col-sm-3">İstihdam</dt><dd class="col-sm-9">{{ $employee->employment_type->value }}</dd>
        <dt class="col-sm-3">İletişim</dt><dd class="col-sm-9">{{ $employee->email ?: '—' }} / {{ $employee->phone ?: '—' }}</dd>
        <dt class="col-sm-3">Durum</dt><dd class="col-sm-9">{{ $employee->status->value }}</dd>
        <dt class="col-sm-3">Sistem erişimi</dt><dd class="col-sm-9">{{ $employee->has_system_access ? 'Evet' : 'Hayır' }}</dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>KVKK uyum kayıtları</strong></div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">İşe giriş / Ayrılış</dt>
        <dd class="col-sm-9">{{ $employee->hired_at?->format('d.m.Y') ?: '—' }} / {{ $employee->left_at?->format('d.m.Y') ?: '—' }}</dd>
        <dt class="col-sm-3">Aydınlatma imza</dt><dd class="col-sm-9">{{ $employee->privacy_notice_signed_at?->format('d.m.Y') ?: '—' }}</dd>
        <dt class="col-sm-3">Gizlilik taahhüdü</dt><dd class="col-sm-9">{{ $employee->confidentiality_signed_at?->format('d.m.Y') ?: '—' }}</dd>
        <dt class="col-sm-3">KVKK eğitim</dt><dd class="col-sm-9">{{ $employee->training_completed_at?->format('d.m.Y') ?: '—' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $employee->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
