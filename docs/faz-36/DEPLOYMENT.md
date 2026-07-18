# KVKK 360 — Deployment Runbook

## Ön koşullar
- Docker + Docker Compose v2
- veya PHP 8.4 + Composer + Node 22 (lokal demo)

## 1) Ortam dosyası
```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

Compose için `.env` içinde en azından:
- `APP_KEY`, `JWT_SECRET`
- `APP_URL=http://localhost:8080`

Compose servisleri `DB_*` / `REDIS_HOST` değerlerini override eder.

## 2) Docker ile ayağa kaldırma
```bash
make up
# veya: docker compose up -d --build
```

Servisler:
| Servis | Rol |
|--------|-----|
| nginx | HTTP `:8080` |
| app | PHP-FPM |
| mysql | MySQL 8.4 |
| redis | Cache / queue / session |
| queue | `queue:work redis` |
| scheduler | `schedule:work` (backup 02:00, dues 08:00) |

Kontroller:
```bash
curl -sf http://localhost:8080/up
docker compose exec app php artisan deploy:check
docker compose exec app php artisan db:seed --force   # demo kullanıcılar
```

## 3) Lokal SQLite demo (Docker yok)
```bash
make demo
# http://127.0.0.1:8000
```

## 4) Production checklist
- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_KEY` ve `JWT_SECRET` dolu
- [ ] HTTPS / reverse proxy
- [ ] `php artisan deploy:check` yeşil
- [ ] `GET /up` health probe
- [ ] Queue + scheduler süreçleri çalışıyor
- [ ] `backup:run` schedule aktif; yedek disk yazılabilir
- [ ] Mailer production (SMTP); AI için isteğe bağlı `OPENAI_API_KEY`
- [ ] `php artisan config:cache` / `route:cache` / `view:cache` (entrypoint `CACHE_CONFIG=true`)

## 5) CI
Push/PR → `.github/workflows/ci.yml` (Pint, PHPStan, PHPUnit).

## 6) Yedek
```bash
docker compose exec app php artisan backup:run
```
Arşivler `storage/app/private/backups` (compose volume: `app-storage`).
