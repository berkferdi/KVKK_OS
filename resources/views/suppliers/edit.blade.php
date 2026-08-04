@extends('layouts.admin')
@section('title', 'Tedarikçi Düzenle — KVKK 360')
@section('page_title', 'Tedarikçi Düzenle')
@section('page_subtitle', $supplier->name)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.suppliers.update', [$company, $supplier]) }}">
    @csrf @method('PUT')
    @include('suppliers._form', ['supplier' => $supplier])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.suppliers.show', [$company, $supplier]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
