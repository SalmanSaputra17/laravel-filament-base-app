.PHONY: up down build logs restart migrate fresh install

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build --no-cache

logs:
	docker-compose logs -f

restart:
	docker-compose restart

migrate:
	docker-compose exec app php artisan migrate

fresh:
	docker-compose exec app php artisan migrate:fresh --seed

install:
	docker-compose up -d --build
	docker-compose exec app cp .env.docker .env
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate
	docker-compose exec app php artisan shield:install admin --silent
	docker-compose exec app php artisan shield:generate --panel=admin

shell:
	docker-compose exec app bash

npm:
	docker-compose run --rm npm run dev

composer:
	docker-compose run --rm composer install

test:
	docker-compose exec app php artisan test

tinker:
	docker-compose exec app php artisan tinker

telescope:
	docker-compose exec app php artisan telescope:install
	docker-compose exec app php artisan migrate

pulse:
	docker-compose exec app php artisan pulse:install
	docker-compose exec app php artisan migrate
