# FAZ 36 — Deployment Özeti

## Kapsam
Üretim iskeleti: Docker Compose, CI, `deploy:check`, runbook. Yol haritasının son fazı.

## Çıktılar
- `Dockerfile` (multi-stage: composer + vite + php-fpm)
- `docker-compose.yml`: app, nginx, mysql, redis, queue, scheduler
- `.github/workflows/ci.yml` — Pint, PHPStan, PHPUnit
- `deploy:check` artisan komutu + `DeployCheckTest`
- `docs/faz-36/DEPLOYMENT.md` runbook, `Makefile`
- Health: `GET /up`

## Hızlı komutlar
```bash
make up          # compose
make check       # deploy:check
make ci          # kalite
make demo        # lokal sqlite
```

## Sonraki
Yol haritası tamamlandı. İyileştirmeler ürün backlog’una geçer.
