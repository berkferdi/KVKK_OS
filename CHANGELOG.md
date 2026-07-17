# Changelog

## [0.1.0] — 2026-07-17

### FAZ 01 — Analiz
- Ürün gereksinimleri, domain model, güvenlik, yol haritası ve ADR’ler eklendi (`docs/`).

### FAZ 02 — Database
- Laravel 12 iskeleti, DDD klasör yapısı
- Tablolar: `tenants`, `tenant_user`, `companies`, `branches`, `audit_logs`
- Users genişletildi (uuid, soft delete, super admin)
- Spatie Permission (teams = `tenant_id`)
- BaseRepository, TenantScope, TenantContext, AuditLogger, CompanyService
- Seed + factory’ler

### FAZ 03 — Authentication
- Web session login/logout (rate limit, audit)
- JWT API auth (`/api/v1/auth/*`)
- Tenant session middleware
- Login UI (AdminLTE / Bootstrap 5.3)

### FAZ 04 — Dashboard
- Kimlik doğrulama sonrası temel dashboard iskeleti

### FAZ 05 — Firma Yönetimi
- Firma CRUD, policy, form request, AdminLTE views, tenant izolasyon testleri

### FAZ 06 — Şube Yönetimi
- Firmaya nested şube CRUD, tek merkez şube kuralı, audit log
