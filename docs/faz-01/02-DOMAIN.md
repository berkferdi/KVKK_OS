# Domain Model — KVKK 360

## Bounded Contexts

| Context | Sorumluluk |
|---------|------------|
| **Identity & Access** | User, Role, Permission, JWT, session |
| **Tenant / Organization** | Tenant, Company (Firma), Branch (Şube) |
| **Compliance Analysis** | Wizard runs, AI suggestions, rule evaluations |
| **Inventory** | Processing activities, legal bases, data categories |
| **Risk** | Risk assessments, scores, mitigations |
| **Assets** | Personnel, customers, suppliers, visitors, cameras, website, cookies |
| **Documents** | Templates, placeholders, Word/PDF/ZIP outputs |
| **Regulatory** | VERBİS, applications (başvuru), breach, audit, training |
| **Platform Ops** | Notifications, backup, audit log |

## Çekirdek Aggregate’ler (FAZ 02–05)

### Tenant
- Platform müşterisi (danışman firması veya doğrudan abone).
- Tüm iş verisi `tenant_id` ile izole.

### Company (Firma)
- KVKK uyum konusu olan işletme.
- Alanlar: unvan, vergi no, MERSİS, NACE, adres, yetkili, telefon, e-posta, faaliyet özeti, bayraklar (kamera, web, çerez vb.).

### Branch (Şube)
- Firmaya bağlı lokasyon; envanter/kamera adresi bağlamı.

### User
- Spatie roller; bir veya daha fazla tenant üyeliği (pivot).

### AuditLog
- Kim, ne, hangi kayıt, ne zaman, IP, payload özeti.

## Değer Nesneleri (örnek)

- `TaxNumber`, `MersisNumber`, `NaceCode`
- `Address`, `PhoneNumber`
- `RiskScore`, `LegalBasisCode`
- `DocumentPlaceholderMap`

## Domain Olayları (ileri faz)

- `CompanyCreated` → wizard tetiklenebilir
- `RuleMatched` → belge/envanter görevleri oluştur
- `DocumentPackageReady` → bildirim
- `BreachReported` → zorunlu süreç akışı

## Multi-Tenant Kuralı

Her tenant-scoped model:

1. `tenant_id` zorunlu (FK)
2. Global scope veya repository filtresi
3. Policy’de tenant eşleşmesi kontrolü
4. Super Admin dışında cross-tenant erişim yok
