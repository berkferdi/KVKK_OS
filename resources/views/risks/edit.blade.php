@extends('layouts.admin')

@section('title', 'Risk Düzenle — KVKK 360')
@section('page_title', 'Risk Düzenle')
@section('page_subtitle', $risk->title)

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('companies.risks.update', [$company, $risk]) }}">
            @csrf
            @method('PUT')
            @include('risks._form', ['risk' => $risk])
            <div class="mt-4 d-flex gap-2">
                <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
                <a href="{{ route('companies.risks.show', [$company, $risk]) }}" class="btn btn-outline-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>
@endsection
