@extends('layouts.admin')

@section('title', 'Yetkiler — KVKK 360')
@section('page_title', 'Yetkiler')
@section('page_subtitle', 'Sistem yetki kataloğu')

@section('content')
@can('create', Spatie\Permission\Models\Permission::class)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('permissions.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-8">
                <label class="form-label" for="name">Yeni yetki (örn. inventory.view)</label>
                <input type="text" name="name" id="name" class="form-control" required pattern="[a-z0-9_.\-]+"
                       value="{{ old('name') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn text-white w-100" style="background:#1f6f5b;">Ekle</button>
            </div>
        </form>
    </div>
</div>
@endcan

@foreach ($permissions as $group => $items)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white text-uppercase small fw-bold">{{ $group }}</div>
        <ul class="list-group list-group-flush">
            @foreach ($items as $permission)
                <li class="list-group-item d-flex justify-content-between">
                    <code>{{ $permission->name }}</code>
                    <span class="text-muted small">#{{ $permission->id }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endforeach
@endsection
