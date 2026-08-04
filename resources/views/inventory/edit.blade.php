@extends('layouts.admin')

@section('title', 'Envanter Düzenle — KVKK 360')
@section('page_title', 'Envanter Düzenle')
@section('page_subtitle', $activity->name)

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('companies.inventory.update', [$company, $activity]) }}">
            @csrf
            @method('PUT')
            @include('inventory._form', ['activity' => $activity])
            <div class="mt-4 d-flex gap-2">
                <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
                <a href="{{ route('companies.inventory.show', [$company, $activity]) }}" class="btn btn-outline-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>
@endsection
