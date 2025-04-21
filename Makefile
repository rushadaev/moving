.PHONY: up-dev down-dev build-dev rebuild-dev up-prod down-prod build-prod rebuild-prod restart logs frontend backend mysql

# Development commands
up-dev:
	docker-compose -f docker-compose.dev.yml up -d

down-dev:
	docker-compose -f docker-compose.dev.yml down

build-dev:
	docker-compose -f docker-compose.dev.yml build

rebuild-dev:
	docker-compose -f docker-compose.dev.yml down
	docker-compose -f docker-compose.dev.yml build --no-cache
	docker-compose -f docker-compose.dev.yml up -d

# Production commands
up-prod:
	docker-compose -f docker-compose.prod.yml up -d

down-prod:
	docker-compose -f docker-compose.prod.yml down

build-prod:
	docker-compose -f docker-compose.prod.yml build

rebuild-prod:
	docker-compose -f docker-compose.prod.yml down
	docker-compose -f docker-compose.prod.yml build --no-cache
	docker-compose -f docker-compose.prod.yml up -d

# Common commands
restart:
	docker-compose restart

logs:
	docker-compose logs -f

frontend:
	docker-compose exec frontend sh

backend:
	docker-compose exec backend sh

mysql:
	docker-compose exec mysql mysql -ularavel -psecret laravel 