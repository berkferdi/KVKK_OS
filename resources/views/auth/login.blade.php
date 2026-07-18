@extends('layouts.app')

@section('title', 'Giriş — KVKK 360')
@section('body_class', 'login-page')

@section('body')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="text-center mb-4">
                <div class="login-brand">KVKK 360</div>
                <p class="text-muted mb-0">Yapay Zeka Destekli KVKK Uyum ve Yönetim Platformu</p>
            </div>
            <div class="card login-card">
                <div class="card-body p-4">
                    <h1 class="h5 mb-3" style="color:#0f2a3d;">Panele giriş</h1>
                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="email">E-posta</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Şifre</label>
                            <input id="password" type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                            <label class="form-check-label" for="remember">Beni hatırla</label>
                        </div>
                        <button type="submit" class="btn btn-kvkk w-100">Giriş yap</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
