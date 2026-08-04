@extends('layouts.admin')
@section('title', 'Yeni Ziyaretçi — KVKK 360')
@section('page_title', 'Yeni Ziyaretçi')
@section('page_subtitle', $company->trade_name)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.visitors.store', $company) }}">
    @csrf
    @include('visitors._form', ['visitor' => null])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Kaydet</button>
        <a href="{{ route('companies.visitors.index', $company) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
