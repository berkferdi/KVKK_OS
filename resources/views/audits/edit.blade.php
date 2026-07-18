@extends('layouts.admin')
@section('title', 'Denetim Düzenle — KVKK 360')
@section('page_title', 'Denetim Düzenle')
@section('page_subtitle', $audit->title)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.audits.update', [$company, $audit]) }}">
    @csrf @method('PUT')
    @include('audits._form', ['audit' => $audit])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.audits.show', [$company, $audit]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
