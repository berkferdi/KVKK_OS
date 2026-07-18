# FAZ 35 — Backup Özeti

## Kapsam
Platform düzeyinde veritabanı yedeği. ZIP içinde `database.sql` + `meta.json`; isteğe bağlı depolama dosyaları.

## Çıktılar
- `config/backup.php`, `backups` tablosu
- `DatabaseDumper` (SQLite PHP dump; MySQL mysqldump/PHP)
- `BackupService`, `backup:run` komutu (schedule 02:00)
- Web: `/backups` (liste, oluştur, indir, sil)
- Yetki: `backups.manage` (danışmanda yok)
- Feature test: `BackupTest`

## İş kuralları
- Retention: `BACKUP_KEEP` (varsayılan 14)
- Audit: `backup.completed` / `backup.failed` / `backup.deleted`
- Tam restore UI bu fazda yok (SQL arşivi indirilir)

## Sonraki faz
FAZ 36 — Deployment
