.PHONY: help up down build logs check ci migrate seed demo

help:
	@echo "KVKK 360 make targets:"
	@echo "  make up       - docker compose up -d --build"
	@echo "  make down     - docker compose down"
	@echo "  make logs     - follow compose logs"
	@echo "  make check    - php artisan deploy:check"
	@echo "  make ci       - pint + phpstan + phpunit"
	@echo "  make migrate  - migrate inside app container"
	@echo "  make seed     - db:seed inside app container"
	@echo "  make demo     - local sqlite serve (no docker)"

up:
	docker compose up -d --build

down:
	docker compose down

build:
	docker compose build

logs:
	docker compose logs -f

check:
	php artisan deploy:check

ci:
	vendor/bin/pint --test
	vendor/bin/phpstan analyse --memory-limit=512M
	php artisan test

migrate:
	docker compose exec app php artisan migrate --force

seed:
	docker compose exec app php artisan db:seed --force

demo:
	@test -f .env || cp .env.example .env
	@php artisan key:generate --force
	@php artisan jwt:secret --force
	@mkdir -p database && touch database/database.sqlite
	@php artisan migrate --seed --force
	@php artisan serve --host=127.0.0.1 --port=8000
