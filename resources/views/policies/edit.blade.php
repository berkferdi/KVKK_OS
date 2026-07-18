@extends('layouts.admin')
@section('title', 'Politika Düzenle — KVKK 360')
@section('page_title', 'Politika Düzenle')
@section('page_subtitle', $policy->title)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.policies.update', [$company, $policy]) }}">
    @csrf @method('PUT')
    @include('policies._form', ['policy' => $policy])
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('companies.policies.show', [$company, $policy]) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
