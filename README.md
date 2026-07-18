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

## Hızlı başlangıç

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
touch database/database.sqlite   # veya MySQL ayarlayın
php artisan migrate --seed
php artisan serve
```

### Demo hesaplar (seeder)

| Rol | E-posta | Şifre |
|-----|---------|-------|
| Super Admin | admin@kvkk360.test | password |
| Danışman | danisman@kvkk360.test | password |

## Mimari

DDD + Service Layer + Repository. Detay: `docs/architecture/`, faz durumu: `docs/PHASE_STATUS.md`.

## Kalite

```bash
composer test
vendor/bin/pint --test
vendor/bin/phpstan analyse
```

## API (JWT)

- `POST /api/v1/auth/login`
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/logout`
- `POST /api/v1/auth/refresh`

## Faz durumu

Tamamlanan: **01–23**  
Sıradaki: **24 Başvurular**
