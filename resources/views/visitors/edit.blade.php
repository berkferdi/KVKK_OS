@extends('layouts.admin')
@section('title', 'Ziyaretçi Düzenle — KVKK 360')
@section('page_title', 'Ziyaretçi Düzenle')
@section('page_subtitle', $visitor->fullName())
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.visitors.update', [$company, $visitor]) }}">
    @csrf @method('PUT')
    @include('visitors._form', ['visitor' => $visitor])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.visitors.show', [$company, $visitor]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
