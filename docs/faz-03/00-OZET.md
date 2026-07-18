# FAZ 03 — Authentication

## Web

- `GET/POST /login`, `POST /logout`
- Throttle: 10/dk
- Başarılı girişte `tenant_id` session + Spatie team id
- Audit: `auth.login_success` / `auth.login_failed` / `auth.logout`

## API (JWT)

- Guard: `api` (jwt)
- `POST /api/v1/auth/login`
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/logout`
- `POST /api/v1/auth/refresh`

## Güvenlik

- CSRF (web)
- Rate limit
- `is_active` zorunlu
- Soft-deleted kullanıcılar Eloquent default ile hariç
