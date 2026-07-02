.PHONY: help setup up down test test-modern phpstan rector logs ps

help:
	@echo ""
	@echo "legacy-php-payment - Available commands"
	@echo "=================================================="
	@echo "  make setup       First-time setup (build, up, composer install)"
	@echo "  make up          Start containers"
	@echo "  make down        Stop containers"
	@echo "  make test        Run characterization tests (modern)"
	@echo "  make phpstan     Run PHPStan on modern/"
	@echo "  make rector      Dry-run Rector on modern/"
	@echo "  make logs        Tail container logs"
	@echo "  make ps          Show container status"
	@echo ""
	@echo "URLs:"
	@echo "  Legacy: http://localhost:8080/login.php"
	@echo "  Modern: http://localhost:8081/login.php"
	@echo ""

setup: up
	@echo "=== Waiting for MySQL ==="
	@sleep 10
	@echo "=== composer install ==="
	docker compose run --rm cli composer install
	@echo ""
	@echo "=== setup complete ==="
	@echo "Legacy: http://localhost:8080/login.php"
	@echo "Modern: http://localhost:8081/login.php"
	@echo "Login: test@example.com / password123"

up:
	docker compose up -d --build

down:
	docker compose down

test:
	docker compose run --rm cli ./vendor/bin/phpunit

test-modern: test

phpstan:
	docker compose run --rm cli ./vendor/bin/phpstan analyse -c phpstan.neon

rector:
	docker compose run --rm cli ./vendor/bin/rector process --dry-run || test $$? -eq 2

logs:
	docker compose logs -f

ps:
	docker compose ps
