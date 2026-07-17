@extends('layouts.admin')

@section('title', 'Yeni Firma — KVKK 360')
@section('page_title', 'Yeni Firma')
@section('page_subtitle', 'Danışman firma kartını oluşturur')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('companies.store') }}">
            @csrf
            @include('companies._form', ['company' => null, 'statuses' => $statuses])
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn text-white" style="background:#1f6f5b;">Kaydet</button>
                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>
@endsection
