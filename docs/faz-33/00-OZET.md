# FAZ 33 — API Özeti

## Kapsam
JWT korumalı REST API yüzeyi. Kiracı çözümü session yerine `X-Tenant-Id` (uuid) başlığı ile yapılır.

## Çıktılar
- `SetTenantFromHeader` middleware (`tenant.api` alias)
- JSON Resources: Company, Branch, AnalysisRun, AnalysisFinding, Tenant
- Endpoint’ler:
  - Auth: login / me (tenants) / logout / refresh
  - `GET|POST /api/v1/companies`
  - `GET|PUT|PATCH|DELETE /api/v1/companies/{uuid}`
  - `GET /api/v1/companies/{uuid}/branches`
  - `POST /api/v1/companies/{uuid}/analysis`
  - `GET /api/v1/analysis/{uuid}`
- Feature testler: `ApiCompanyTest`, `ApiAnalysisTest`

## İş kuralları
- Authenticated API isteklerinde tenant context zorunlu
- Header yoksa kullanıcının ilk kiracı üyeliği kullanılır
- Yabancı kiracı başlığı → 403; scope dışı kayıt → 404
- Policy ve Spatie team id web ile aynı

## Sonraki faz
FAZ 34 — Bildirimler
