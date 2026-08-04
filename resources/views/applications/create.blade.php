@extends('layouts.admin')
@section('title', 'Yeni Başvuru — KVKK 360')
@section('page_title', 'Yeni Başvuru')
@section('page_subtitle', $company->trade_name)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.applications.store', $company) }}">
    @csrf
    @include('applications._form', ['application' => null])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Kaydet</button>
        <a href="{{ route('companies.applications.index', $company) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
