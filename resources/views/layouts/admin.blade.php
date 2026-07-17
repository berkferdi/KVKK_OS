@extends('layouts.app')

@section('body_class', 'hold-transition sidebar-mini layout-fixed')

@section('body')
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item me-3 d-none d-md-block">
                <span class="text-muted small">{{ auth()->user()->name }}</span>
            </li>
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
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('companies.index') }}" class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Firmalar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Kullanıcılar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>Roller</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-key"></i>
                            <p>Yetkiler</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="m-0" style="color:#0f2a3d;">@yield('page_title')</h1>
                    @hasSection('page_subtitle')
                        <p class="text-muted mb-0">@yield('page_subtitle')</p>
                    @endif
                </div>
                <div>@yield('page_actions')</div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </section>
    </div>
</div>
@endsection
