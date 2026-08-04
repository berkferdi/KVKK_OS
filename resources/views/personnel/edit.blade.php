@extends('layouts.admin')
@section('title', 'Personel Düzenle — KVKK 360')
@section('page_title', 'Personel Düzenle')
@section('page_subtitle', $employee->fullName())
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.personnel.update', [$company, $employee]) }}">
    @csrf @method('PUT')
    @include('personnel._form', ['employee' => $employee])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.personnel.show', [$company, $employee]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
