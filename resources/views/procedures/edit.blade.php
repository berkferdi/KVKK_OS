@extends('layouts.admin')
@section('title', 'Prosedür Düzenle — KVKK 360')
@section('page_title', 'Prosedür Düzenle')
@section('page_subtitle', $procedure->title)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.procedures.update', [$company, $procedure]) }}">
    @csrf @method('PUT')
    @include('procedures._form', ['procedure' => $procedure])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.procedures.show', [$company, $procedure]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
