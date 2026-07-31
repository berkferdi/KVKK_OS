#!/usr/bin/env sh
set -eu

cd /var/www/html

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  echo "[entrypoint] waiting for database..."
  i=0
  until php artisan db:show >/dev/null 2>&1; do
    i=$((i + 1))
    if [ "$i" -ge 60 ]; then
      echo "[entrypoint] database not ready" >&2
      exit 1
    fi
    sleep 2
  done

  php artisan migrate --force --no-interaction
fi

if [ "${CACHE_CONFIG:-false}" = "true" ]; then
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
fi

exec "$@"
