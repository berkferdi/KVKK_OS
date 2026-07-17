# FAZ 01 — Analiz Özeti

**Proje:** KVKK 360  
**Slogan:** Yapay Zeka Destekli KVKK Uyum ve Yönetim Platformu  
**Durum:** Tamamlandı  
**Tarih:** 2026-07-17

## Karar

Mevcut depo boştur (`README.md`, `TEST`). Greenfield başlangıçtır.  
Önce analiz ve mimari temel, ardından Laravel 12 iskeleti + çekirdek veri modeli (FAZ 02).

## Ürün Özeti

Danışman yalnızca firma bilgilerini girer. Sistem faaliyet alanı, KVKK yükümlülükleri, belgeler, risk, envanter, VERBİS ve teslim paketini (Word/PDF/ZIP) üretir.

## Kritik Mimari Kararlar

| Konu | Karar |
|------|--------|
| Mimari | DDD + Service Layer + Repository + DI |
| Multi-tenant | `tenant_id` kolon tabanlı (shared DB) |
| Auth (web) | Session + Spatie Permission |
| Auth (API) | JWT (tymon/jwt-auth) |
| UI | AdminLTE 3 + Bootstrap 5.3 |
| Kural motoru | DB tabanlı; KVKK kuralı kodda yok |
| Belge motoru | Placeholder şablonlar (`{{firma_unvani}}` …) |
| AI | OpenAI API, servis katmanı arkasında |
| Queue/Cache | Redis |
| Kalite | PHPStan, Pint, PHPUnit |

## Faz Sırası (bağımlılık)

```
01 Analiz → 02 Database → 03 Auth → 04 Dashboard
→ 05 Firma → 06 Şube → 07 Kullanıcı → 08 Roller → 09 Yetkiler
→ 10 Analiz Sihirbazı → 13 Rule Engine (erken iskelet)
→ 11 Envanter → 12 Risk → 14–22 varlık modülleri
→ 23 VERBİS → 24–27 süreç → 28–31 belge → 32 AI → 33–36 ops
```

Rule Engine (13) Analiz Sihirbazı (10) ile birlikte iskelet olarak erken gelir; tam kural seti sonraki fazlarda dolar.

## Çıktılar

- `docs/faz-01/` — gereksinim, domain, güvenlik, yol haritası
- `docs/architecture/` — katmanlar, klasör yapısı, ADR’ler
- `docs/PHASE_STATUS.md` — faz takip tablosu
