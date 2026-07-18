@extends('layouts.admin')
@section('title', 'Eğitim Düzenle — KVKK 360')
@section('page_title', 'Eğitim Düzenle')
@section('page_subtitle', $training->title)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.trainings.update', [$company, $training]) }}">
    @csrf @method('PUT')
    @include('trainings._form', ['training' => $training])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.trainings.show', [$company, $training]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
