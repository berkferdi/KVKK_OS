@extends('layouts.app')

@section('title', 'Dashboard — KVKK 360')

@section('body')
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary me-2" type="submit">Çıkış</button>
                </form>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background:#0f2a3d;">
        <a href="{{ route('dashboard') }}" class="brand-link text-center">
            <span class="brand-text">KVKK 360</span>
        </a>
        <div class="sidebar">
            <nav class="mt-3">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0" style="color:#0f2a3d;">Dashboard</h1>
                <p class="text-muted mb-0">Hoş geldiniz, {{ auth()->user()->name }}</p>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $companyCount }}</h3>
                                <p>Firma</p>
                            </div>
                            <div class="icon"><i class="fas fa-building"></i></div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-light border">
                    Sonraki faz: Firma Yönetimi (FAZ 05) — CRUD ve analiz sihirbazı hazırlığı.
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
