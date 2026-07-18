@extends('layouts.admin')
@section('title', 'Başvuru Düzenle — KVKK 360')
@section('page_title', 'Başvuru Düzenle')
@section('page_subtitle', $application->applicant_name)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.applications.update', [$company, $application]) }}">
    @csrf @method('PUT')
    @include('applications._form', ['application' => $application])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.applications.show', [$company, $application]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
