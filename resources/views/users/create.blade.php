@extends('layouts.admin')

@section('title', 'Yeni Kullanıcı — KVKK 360')
@section('page_title', 'Yeni Kullanıcı')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            @include('users._form', ['user' => null, 'roles' => $roles])
            <div class="mt-4 d-flex gap-2">
                <button class="btn text-white" style="background:#1f6f5b;" type="submit">Kaydet</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>
@endsection
