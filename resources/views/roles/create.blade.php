@extends('layouts.admin')

@section('title', 'Yeni Rol — KVKK 360')
@section('page_title', 'Yeni Rol')

@section('content')
<form method="POST" action="{{ route('roles.store') }}">
    @csrf
    @include('roles._form', ['role' => null, 'permissions' => $permissions])
    <div class="d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Kaydet</button>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
@endsection
