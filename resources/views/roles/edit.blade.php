@extends('layouts.admin')

@section('title', 'Rol Düzenle — KVKK 360')
@section('page_title', 'Rol Düzenle')
@section('page_subtitle', $role->name)

@section('content')
<form method="POST" action="{{ route('roles.update', $role) }}">
    @csrf
    @method('PUT')
    @include('roles._form', ['role' => $role, 'permissions' => $permissions])
    <div class="d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
@endsection
