@extends('layouts.admin')
@section('title', 'Veri İhlali Düzenle — KVKK 360')
@section('page_title', 'Veri İhlali Düzenle')
@section('page_subtitle', $breach->title)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.breaches.update', [$company, $breach]) }}">
    @csrf @method('PUT')
    @include('breaches._form', ['breach' => $breach])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.breaches.show', [$company, $breach]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
