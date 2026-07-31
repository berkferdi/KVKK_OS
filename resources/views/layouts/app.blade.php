<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KVKK 360')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <style>
        :root {
            --kvkk-ink: #0f2a3d;
            --kvkk-accent: #1f6f5b;
            --kvkk-sand: #e8eef2;
            --kvkk-glow: #c7d8e2;
        }
        body.login-page {
            min-height: 100vh;
            background:
                radial-gradient(ellipse at 20% 20%, rgba(31, 111, 91, 0.18), transparent 45%),
                radial-gradient(ellipse at 80% 0%, rgba(15, 42, 61, 0.22), transparent 40%),
                linear-gradient(160deg, #f4f7f9 0%, var(--kvkk-sand) 55%, #d9e4ec 100%);
        }
        .login-brand {
            font-family: "Segoe UI", "Helvetica Neue", sans-serif;
            letter-spacing: 0.04em;
            color: var(--kvkk-ink);
            font-weight: 700;
            font-size: 2rem;
        }
        .login-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 18px 40px rgba(15, 42, 61, 0.12);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(6px);
        }
        .btn-kvkk {
            background: var(--kvkk-accent);
            border-color: var(--kvkk-accent);
            color: #fff;
        }
        .btn-kvkk:hover { background: #185746; color: #fff; }
        .content-wrapper { background: #f3f6f8; }
        .brand-link { font-weight: 700; }
    </style>
    @stack('styles')
</head>
<body class="@yield('body_class', 'hold-transition sidebar-mini layout-fixed')">
@yield('body')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
@stack('scripts')
</body>
</html>
