# Gereksinim Analizi — KVKK 360

## 1. İş Hedefi

Türkiye’de satılacak, danışman odaklı, ticari KVKK Yönetim Platformu.  
Danışman firma bilgilerini girer; sistem tek tuşla teslim dosyası üretir.

## 2. Aktörler

| Aktör | Açıklama |
|-------|----------|
| Super Admin | Platform yönetimi, tenant oluşturma |
| Danışman (Consultant) | Firma onboarding, analiz, belge üretimi |
| Firma Yetkilisi | Kendi tenant verisini görüntüleme / sınırlı düzenleme |
| KVKK Sorumlusu / DPO | Envanter, risk, başvuru, ihlal yönetimi |
| API Client | JWT ile entegrasyon |

## 3. Temel Kullanım Senaryoları (MVP çekirdeği)

1. Danışman giriş yapar → yeni firma oluşturur.
2. Firma kartına şube, personel, kamera, web vb. varlık bilgileri girilir.
3. KVKK Analiz Sihirbazı çalışır (AI + Rule Engine).
4. Sistem yükümlülük, belge listesi, envanter ve risk önerilerini üretir.
5. Belge Motoru Word/PDF üretir; ZIP teslim klasörü paketler.

## 4. Fonksiyonel Modüller (özet)

| Grup | Modüller |
|------|----------|
| Platform | Auth, Dashboard, Kullanıcı, Rol, Yetki |
| Organizasyon | Firma, Şube |
| Analiz | Sihirbaz, Rule Engine, AI Engine |
| Envanter & Risk | Veri İşleme Envanteri, Risk Analizi |
| Varlıklar | Politika, Prosedür, Personel, Müşteri, Tedarikçi, Ziyaretçi, Kamera, Web, Çerez |
| Süreç | VERBİS, Başvuru, İhlal, Denetim, Eğitim |
| Çıktı | Belge Motoru, Word, PDF, ZIP |
| Ops | API, Bildirim, Backup, Deployment |

## 5. Non-Fonksiyonel Gereksinimler

- **Güvenlik:** CSRF, XSS, SQLi koruması, rate limit, audit log, soft delete
- **Çok kiracılılık:** Tenant izolasyonu (zorunlu scope)
- **Performans:** Queue ile belge/AI işleri; Redis cache
- **Kalite:** PHPStan (level yükseltilebilir), Pint, Feature + Unit test
- **Sürdürülebilirlik:** DDD bounded context’ler; kural/kod ayrımı

## 6. Teslim Klasör Yapısı (firma bazlı)

```
KVKK360/
  {Firma Adı}/
    01 Kurumsal Belgeler
    02 Politikalar
    03 Prosedürler
    04 Envanter
    05 Risk
    06 Personel
    07 Müşteri
    08 Tedarikçi
    09 Kamera
    10 Web
    11 VERBİS
    12 Eğitim
    13 Denetim
    14 İmzalı Belgeler
    15 Teslim Dosyası
```

## 7. Belge Motoru Notları

~100 belge; placeholder tabanlı (`{{firma_unvani}}`, `{{adres}}`, `{{mersis}}`, …).  
Şablonlar storage’da; üretim PHPWord / mPDF; paketleme ZIP.

## 8. Rule Engine Notları

Örnek: `kamera=true` → kamera aydınlatması, riski, envanteri, saklama, denetim, VERBİS kayıtları otomatik.
Kurallar DB’de; uygulama yalnızca evaluator çalıştırır.
