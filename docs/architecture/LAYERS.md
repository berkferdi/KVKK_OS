# Mimari — Katmanlar ve Klasör Yapısı

## Katmanlar

```
HTTP (Controllers, Form Requests, API Resources, Middleware)
        ↓
Application / Service Layer (use-cases, orchestration)
        ↓
Domain (Entities/Models, Policies, Enums, Events)
        ↓
Infrastructure (Repositories, External APIs, Storage, Queue)
        ↓
Persistence (Eloquent, MySQL / SQLite tests)
```

## Laravel Uygulama Klasör Önerisi

```
app/
  Domain/
    Identity/
    Organization/
    Compliance/
    Inventory/
    Risk/
    Documents/
    Regulatory/
    Shared/
      Traits/
      Enums/
      Contracts/
  Application/
    Services/
    DTOs/
    Actions/
  Infrastructure/
    Repositories/
    External/OpenAI/
    Storage/
  Http/
    Controllers/
      Web/
      Api/
    Middleware/
    Requests/
    Resources/
  Policies/
  Providers/
```

## Desenler

- **Repository:** Interface Domain’de veya Contracts’ta; implementasyon Infrastructure’da
- **Service:** İş kuralları orkestrasyonu; controller ince kalır
- **Policy:** Yetkilendirme
- **Form Request:** Validasyon
- **Action:** Tek sorumluluklu use-case sınıfları (opsiyonel, karmaşık akışlarda)

## UI

- Blade + AdminLTE 3 + Bootstrap 5.3
- Layout: `resources/views/layouts/admin.blade.php`
- Modül view’ları: `resources/views/{module}/`

## Config / Env

- `OPENAI_API_KEY`, `JWT_SECRET`, Redis, queue `redis`
- Belge şablon path: `storage/app/templates`

## Docker (FAZ 36)

`docker-compose.yml`: app (PHP-FPM), nginx, mysql, redis, queue worker, scheduler.  
Runbook: `docs/faz-36/DEPLOYMENT.md`. Health: `GET /up`. Kontrol: `php artisan deploy:check`.
