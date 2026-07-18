@extends('layouts.admin')
@section('title', 'Yeni Şablon — KVKK 360')
@section('page_title', 'Yeni Şablon')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('document-templates.store') }}">
    @csrf
    @include('document-templates._form', ['template' => null])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Kaydet</button>
        <a href="{{ route('document-templates.index') }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
