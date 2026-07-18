@extends('layouts.admin')
@section('title', 'Çerez Düzenle — KVKK 360')
@section('page_title', 'Çerez Düzenle')
@section('page_subtitle', $cookie->name)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.cookies.update', [$company, $cookie]) }}">
    @csrf @method('PUT')
    @include('cookies._form', ['cookie' => $cookie])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.cookies.show', [$company, $cookie]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
