@extends('layouts.admin')
@section('title', $training->title.' — Eğitim')
@section('page_title', $training->title)
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    <a href="{{ route('companies.trainings.edit', [$company, $training]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    <form method="POST" action="{{ route('companies.trainings.destroy', [$company, $training]) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
    </form>
@endsection
@section('content')
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kod</dt><dd class="col-sm-9">{{ $training->training_code ?: '—' }}</dd>
        <dt class="col-sm-3">Tür / Yöntem</dt><dd class="col-sm-9">{{ $training->training_type->value }} / {{ $training->delivery_method->value }}</dd>
        <dt class="col-sm-3">Şube</dt><dd class="col-sm-9">{{ $training->branch?->name ?: '—' }}</dd>
        <dt class="col-sm-3">Durum</dt>
        <dd class="col-sm-9">
            {{ $training->status->value }}
            @if($training->isScheduleOverdue())
                <span class="badge text-bg-warning">Plan gecikti</span>
            @endif
            @if($training->isNextTrainingOverdue())
                <span class="badge text-bg-danger">Sonraki eğitim gecikti</span>
            @endif
        </dd>
        <dt class="col-sm-3">Eğitmen</dt><dd class="col-sm-9">{{ $training->trainer_name ?: '—' }}</dd>
        <dt class="col-sm-3">Katılımcı</dt><dd class="col-sm-9">{{ $training->participant_count ?? '—' }}</dd>
    </dl>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Takvim ve içerik</strong></div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Plan / Gerçekleşme</dt>
        <dd class="col-sm-9">
            {{ $training->planned_at?->format('d.m.Y H:i') ?: '—' }} /
            {{ $training->conducted_at?->format('d.m.Y H:i') ?: '—' }}
        </dd>
        <dt class="col-sm-3">Sonraki eğitim</dt>
        <dd class="col-sm-9">{{ $training->next_training_due_at?->format('d.m.Y H:i') ?: '—' }}</dd>
        <dt class="col-sm-3">Katılımcılar</dt><dd class="col-sm-9">{{ $training->participant_names ?: '—' }}</dd>
        <dt class="col-sm-3">Konular</dt><dd class="col-sm-9">{{ $training->topics ?: '—' }}</dd>
        <dt class="col-sm-3">Materyaller</dt><dd class="col-sm-9">{{ $training->materials ?: '—' }}</dd>
        <dt class="col-sm-3">Katılım notları</dt><dd class="col-sm-9">{{ $training->attendance_notes ?: '—' }}</dd>
        <dt class="col-sm-3">Notlar</dt><dd class="col-sm-9">{{ $training->notes ?: '—' }}</dd>
    </dl>
</div></div>
@endsection
