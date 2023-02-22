
up: down .env .make.composer-install .make.npm-install .make.npm-build .make.artisan-key-generate .make.jwt-secret .make.migrate server

down:
	docker compose down

clean:
	rm ./.make.*

server:
	docker compose up -d server

logs:
	docker compose logs -f server

.make.composer-install: composer.json composer.lock
	docker compose run --rm composer-install
	touch $@

.make.npm-install: package.json package-lock.json
	docker compose run --rm npm-install
	touch $@

.make.npm-build:
	docker compose run --rm npm-build
	touch $@

.make.artisan-key-generate:
	docker compose run --rm php-cli artisan key:generate
	touch $@

.make.jwt-secret:
	docker compose run --rm php-cli artisan jwt:secret --f
	touch $@

.make.migrate:
	docker compose run --rm migrate
	touch $@

.env:
	cp .env.example .env

.PHONY: up down clean server
