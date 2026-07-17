@extends('layouts.admin')
@section('title', 'Kamera Düzenle — KVKK 360')
@section('page_title', 'Kamera Düzenle')
@section('page_subtitle', $camera->name)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.cameras.update', [$company, $camera]) }}">
    @csrf @method('PUT')
    @include('cameras._form', ['camera' => $camera])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.cameras.show', [$company, $camera]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
