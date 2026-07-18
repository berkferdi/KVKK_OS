# FAZ 06 — Şube Yönetimi

## Kapsam

Firmaya bağlı şube CRUD; tek merkez şube (is_hq) garantisi.

## Bileşenler

- `BranchController`, `BranchService`, `BranchRepository`
- `StoreBranchRequest` / `UpdateBranchRequest`
- `BranchPolicy`
- Nested routes under `/companies/{company}/branches`
- Audit: `branch.created|updated|deleted`
