# FAZ 05 — Firma Yönetimi

## Kapsam

Tenant-scoped firma CRUD.

## Bileşenler

- `CompanyController`, `CompanyService`, `CompanyRepository`
- `StoreCompanyRequest` / `UpdateCompanyRequest`
- `CompanyPolicy` (tenant + Spatie permission)
- Views: index/create/edit/show + form partial
- Feature test: listeleme, oluşturma, cross-tenant 404

## Rotalar

`/companies` resource
