@extends('layouts.admin')
@section('title', 'Web Sitesi Düzenle — KVKK 360')
@section('page_title', 'Web Sitesi Düzenle')
@section('page_subtitle', $website->name)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.websites.update', [$company, $website]) }}">
    @csrf @method('PUT')
    @include('websites._form', ['website' => $website])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.websites.show', [$company, $website]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
