# KVKK 360

**Yapay Zeka Destekli KVKK Uyum ve Yönetim Platformu**

Profesyonel, çok kiracılı KVKK yönetim yazılımı. Danışman firma bilgilerini girer; sistem analiz, envanter, risk, belge ve VERBİS çıktılarını üretir.

## Stack

- Laravel 12 / PHP 8.4
- MySQL 8 (geliştirmede SQLite)
- Redis, Queue, Scheduler
- Bootstrap 5.3 + AdminLTE
- Spatie Permission (tenant teams)
- JWT (`php-open-source-saver/jwt-auth`)
- PHPStan (Larastan), Pint, PHPUnit

## Hızlı başlangıç (SQLite)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

veya: `make demo`

### Demo hesaplar (seeder)

| Rol | E-posta | Şifre |
|-----|---------|-------|
| Super Admin | admin@kvkk360.test | password |
| Danışman | danisman@kvkk360.test | password |

## Docker (üretim iskeleti)

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
make up
curl -sf http://localhost:8080/up
docker compose exec app php artisan deploy:check
docker compose exec app php artisan db:seed --force
```

Detay: [`docs/faz-36/DEPLOYMENT.md`](docs/faz-36/DEPLOYMENT.md)

## API (JWT)

Kimlik doğrulama sonrası tenant için `X-Tenant-Id: <tenant-uuid>` başlığı gönderin (yoksa ilk üyelik kullanılır).

- `POST /api/v1/auth/login`
- `GET /api/v1/auth/me` — kullanıcı + kiracılar
- `POST /api/v1/auth/logout`
- `POST /api/v1/auth/refresh`
- `GET|POST /api/v1/companies`
- `GET|PUT|PATCH|DELETE /api/v1/companies/{uuid}`
- `GET /api/v1/companies/{uuid}/branches`
- `POST /api/v1/companies/{uuid}/analysis`
- `GET /api/v1/analysis/{uuid}`

## Mimari

DDD + Service Layer + Repository. Detay: `docs/architecture/`, faz durumu: `docs/PHASE_STATUS.md`.

## Kalite

```bash
composer test
vendor/bin/pint --test
vendor/bin/phpstan analyse
# veya: make ci
```

CI: `.github/workflows/ci.yml`

## Production checklist

- `APP_ENV=production`, `APP_DEBUG=false`
- `APP_KEY` + `JWT_SECRET`
- Queue worker + scheduler
- `php artisan deploy:check`
- Health probe: `GET /up`

## Faz durumu

Tamamlanan: **01–36** (yol haritası kapandı)
