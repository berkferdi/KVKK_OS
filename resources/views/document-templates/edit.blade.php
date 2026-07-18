@extends('layouts.admin')
@section('title', 'Şablon Düzenle — KVKK 360')
@section('page_title', 'Şablon Düzenle')
@section('page_subtitle', $template->title)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('document-templates.update', $template) }}">
    @csrf @method('PUT')
    @include('document-templates._form', ['template' => $template])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('document-templates.show', $template) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
