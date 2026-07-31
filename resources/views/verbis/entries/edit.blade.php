@extends('layouts.admin')
@section('title', 'VERBİS Kaydı Düzenle — KVKK 360')
@section('page_title', 'VERBİS Kaydı Düzenle')
@section('page_subtitle', $entry->title)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.verbis.entries.update', [$company, $entry]) }}">
    @csrf @method('PUT')
    @include('verbis.entries._form', ['entry' => $entry])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.verbis.entries.show', [$company, $entry]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
