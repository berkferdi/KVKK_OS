# FAZ 34 — Bildirimler Özeti

## Kapsam
Database + mail kanallı bildirim temeli. Analiz tamamlanınca anlık bildirim; geciken vadeler için günlük tarama komutu.

## Çıktılar
- Laravel `notifications` tablosu
- `AnalysisCompletedNotification`, `ComplianceDueNotification`
- `NotificationService` / `DueReminderService`
- Artisan: `notifications:dispatch-dues` (schedule 08:00)
- Web: `/notifications`, navbar zili, okundu işaretleme
- Yetki: `notifications.view`
- Feature test: `NotificationTest`

## Tetikleyiciler
1. Analiz tamamlandı → kiracı alıcıları + tetikleyen
2. Veri ihlali 72s kurum bildirimi gecikti
3. İlgili kişi başvurusu vadesi geçti
4. Sonraki denetim / eğitim vadesi geçti

## İş kuralları
- 24 saat `dedupe_key` ile tekrar gönderim engeli
- Varsayılan mailer: `log` (testte `array`)
- Queue: `ShouldQueue` (testte `sync`)

## Sonraki faz
FAZ 35 — Backup (tamamlandı) → FAZ 36 Deployment
