# ADR 0001 — Multi-Tenancy Stratejisi

## Durum

Kabul edildi (FAZ 01)

## Bağlam

KVKK 360 birden fazla danışman / aboneye hizmet verecek. Veri izolasyonu zorunlu.

## Karar

**Shared database + `tenant_id` kolon izolasyonu.**

- Her tenant-scoped tabloda `tenant_id`
- Eloquent global scope veya repository zorunlu filtre
- Spatie Permission: roller tenant bağlamında (team/tenant özelliği veya pivot)

## Alternatifler

| Alternatif | Neden seçilmedi |
|------------|-----------------|
| DB-per-tenant | Operasyon maliyeti yüksek (MVP için) |
| Schema-per-tenant | MySQL’de karmaşık migrate/ops |

## Sonuçlar

- Basit backup ve migrate
- Yanlış scope bug riski → Policy + test ile sıkı kontrol
- İleride büyük tenant’lar için sharding eklenebilir

---

# ADR 0002 — Auth: Session + JWT

## Durum

Kabul edildi (FAZ 01)

## Karar

- **Web paneli:** Laravel session auth + Spatie Permission
- **REST API:** JWT (`tymon/jwt-auth` veya eşdeğeri)

İkisi aynı User modelini paylaşır; yetkiler Spatie üzerinden.

## Sonuçlar

- AdminLTE form login basit kalır
- Mobil/entegrasyon API’si JWT ile bağımsız ölçeklenir

---

# ADR 0003 — Rule Engine DB’de

## Durum

Kabul edildi (FAZ 01)

## Karar

KVKK iş kuralları kodda hard-code edilmez.  
`rules`, `rule_conditions`, `rule_actions` (veya JSON condition) tablolarında tutulur.  
Evaluator servisi koşulları firmanın özelliklerine uygular.

## Sonuçlar

- Hukuk/ürün ekibi kural güncelleyebilir (seeder + admin UI)
- Kod deploy’u olmadan kural seti genişler
