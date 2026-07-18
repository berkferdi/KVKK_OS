# Güvenlik Analizi — KVKK 360

## Tehdit Yüzeyi

| Tehdit | Önlem |
|--------|--------|
| CSRF | Laravel CSRF token (web) |
| XSS | Blade escape, CSP (ileride), sanitize |
| SQL Injection | Eloquent / query builder; raw SQL yasak (istisna review) |
| Brute force | Rate limiting (login, API) |
| Privilege escalation | Spatie + Policies + tenant scope |
| Data leak (tenant) | Global scope + feature test izolasyonu |
| Sensitive docs | Private disk, signed URLs, audit |
| AI data | Prompt’a gereksiz PII gönderme politikası |

## Audit Log

Zorunlu olaylar: create/update/delete kritik entity, login fail/success, belge üretimi, export, rol değişimi.

## Soft Delete

Kritik iş tablolarında `SoftDeletes`. Restore yetkisi ayrı permission.

## Secrets

`.env` dışında secret yok. JWT secret, OpenAI key, DB credentials env’de.

## Checklist (her faz)

- [ ] Policy yazıldı mı?
- [ ] Form Request validasyonu var mı?
- [ ] Tenant scope uygulandı mı?
- [ ] Audit log tetikleniyor mu?
- [ ] Feature test yetkisiz erişimi reddediyor mu?
