@extends('layouts.admin')

@section('title', 'Şube Düzenle — KVKK 360')
@section('page_title', 'Şube Düzenle')
@section('page_subtitle', $company->trade_name.' / '.$branch->name)

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('companies.branches.update', [$company, $branch]) }}">
            @csrf
            @method('PUT')
            @include('branches._form', ['branch' => $branch])
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn text-white" style="background:#1f6f5b;">Güncelle</button>
                <a href="{{ route('companies.show', $company) }}" class="btn btn-outline-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>
@endsection
