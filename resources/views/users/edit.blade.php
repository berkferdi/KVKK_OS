@extends('layouts.admin')

@section('title', 'Kullanıcı Düzenle — KVKK 360')
@section('page_title', 'Kullanıcı Düzenle')
@section('page_subtitle', $user->email)

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('users._form', ['user' => $user, 'roles' => $roles])
            <div class="mt-4 d-flex gap-2">
                <button class="btn text-white" style="background:#1f6f5b;" type="submit">Güncelle</button>
                <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>
@endsection
