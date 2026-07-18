# FAZ 02 — Database

## Kapsam

Çekirdek şema ve DDD altyapısı.

## Tablolar

- `tenants`, `tenant_user`, `companies`, `branches`, `audit_logs`
- Spatie permission tabloları (`tenant_id` team key)
- `users` genişletmesi

## Desenler

- `BelongsToTenant` + `TenantScope`
- `HasUuid`
- `BaseRepository` / `CompanyRepository` / `TenantRepository` / `BranchRepository`
- `CompanyService`, `AuditLogger`, `TenantContext`
