@extends('layouts.admin')
@section('title', 'Müşteri Düzenle — KVKK 360')
@section('page_title', 'Müşteri Düzenle')
@section('page_subtitle', $customer->name)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.customers.update', [$company, $customer]) }}">
    @csrf @method('PUT')
    @include('customers._form', ['customer' => $customer])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.customers.show', [$company, $customer]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
