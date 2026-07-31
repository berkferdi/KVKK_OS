# FAZ 25 — Veri İhlali Özeti

## Kapsam
Şirket bazlı veri ihlali kayıtları: tip, önem derecesi, durum, keşif/bildirim tarihleri, etkilenen kayıt sayısı ve KVKK Kurumu 72 saat bildirim takibi.

## Çıktılar
- `data_breaches` migration + `DataBreach` model/enum/factory
- `DataBreachService` / `DataBreachRepository`
- Web CRUD: `/companies/{company}/breaches`
- Policy: `breaches.view` / `breaches.manage`
- Feature test: `DataBreachTest`

## İş kuralları
- `authority_notification_due_at` = `discovered_at + 72 saat` (otomatik)
- Kurum bildirimi gecikmesi: due geçmiş ve `authority_notified_at` boş

## Sonraki faz
FAZ 26 — Denetim
