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

### FAZ 07 — Kullanıcı Yönetimi
- Tenant kullanıcı CRUD, rol atama, self-delete engeli, audit

### FAZ 08 — Roller
- Spatie tenant roller CRUD + permission sync

### FAZ 09 — Yetkiler
- Yetki kataloğu ve yeni yetki ekleme UI

### FAZ 10 — KVKK Analiz Sihirbazı
- Firma bazlı analiz koşumu, bulgu listesi, audit

### FAZ 13 — Rule Engine
- `compliance_rules` DB evaluator; kamera/web/çerez/personel seed kuralları

### FAZ 11 — Veri İşleme Envanteri
- Firma nested envanter CRUD, hukuki sebep, kategori listeleri

### FAZ 12 — Risk Analizi
- Risk CRUD, otomatik skor/seviye, envanter ilişkisi

### FAZ 14 — Politikalar
- Firma politika CRUD (kategori, versiyon, içerik)

### FAZ 15 — Prosedürler
- Firma prosedür CRUD, politikaya bağlanabilir

### FAZ 16 — Personel
- Firma nested personel CRUD (`employees`)
- KVKK alanları: aydınlatma, gizlilik taahhüdü, eğitim tarihleri
- Şube bağlantısı, istihdam türü, sistem erişimi, audit

### FAZ 17 — Müşteri
- Firma nested müşteri CRUD (`customers`)
- Bireysel/kurumsal tür, aydınlatma, açık rıza, pazarlama izni
- Şube bağlantısı, veri kategorileri, audit

### FAZ 18 — Tedarikçi
- Firma nested tedarikçi CRUD (`suppliers`)
- Veri işleme sözleşmesi (DPA), kişisel veri işleme bayrağı
- Sözleşme tarihleri, şube, audit

### FAZ 19 — Ziyaretçi
- Firma nested ziyaretçi CRUD (`visitors`)
- Giriş/çıkış, aydınlatma, kart ve fotoğraf bayrakları
- Şube bağlantısı, durum (expected/checked_in/out), audit

### FAZ 20 — Kamera
- Firma nested kamera CRUD (`cameras`)
- Saklama süresi, aydınlatma tabelası, kayıt/ses bayrakları
- Şube, konum, kapsama alanı, audit

### FAZ 21 — Web Sitesi
- Firma nested web sitesi CRUD (`websites`)
- Gizlilik politikası URL/tarih, SSL, çerez ve form bayrakları
- Toplanan veri kategorileri, audit

### FAZ 22 — Çerez
- Firma nested çerez envanteri CRUD (`site_cookies` / `SiteCookie`)
- Kategori, rıza, üçüncü taraf, süre; opsiyonel web sitesi bağlantısı
- Yetkiler: `cookies.view` / `cookies.manage`

### FAZ 23 — VERBİS
- Firma VERBİS sicil kaydı (`verbis_registrations`) + kayıt kalemleri (`verbis_entries`)
- Muafiyet, irtibat, sicil no; envanter bağlantılı kalemler
- Yetkiler: `verbis.view` / `verbis.manage`

### FAZ 24 — Başvurular
- Firma nested ilgili kişi başvurusu CRUD (`data_subject_applications`)
- Talep türü, kanal, otomatik +30 gün son yanıt tarihi, gecikme bayrağı
- Yetkiler: `applications.view` / `applications.manage`

### FAZ 25 — Veri İhlali
- Firma nested veri ihlali CRUD (`data_breaches`)
- Tip, önem, durum; otomatik 72 saat kurum bildirim vadesi
- Yetkiler: `breaches.view` / `breaches.manage`

### FAZ 26 — Denetim
- Firma nested uyum denetimi CRUD (`compliance_audits`)
- Tür, sonuç, plan/takvim; tamamlanınca otomatik +1 yıl sonraki denetim vadesi
- Yetkiler: `audits.view` / `audits.manage`

### FAZ 27 — Eğitim
- Firma nested eğitim kaydı CRUD (`training_records`)
- Tür, yöntem, katılımcı; tamamlanınca otomatik +1 yıl sonraki eğitim vadesi
- Yetkiler: `trainings.view` / `trainings.manage`
