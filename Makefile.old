.PHONY: install up-dev down-dev build-dev rebuild-dev up-prod down-prod build-prod rebuild-prod restart logs frontend backend mysql clear-cache-dev clear-cache-prod generate-key

# Generate Laravel application key
generate-key:
	docker-compose -f docker-compose.dev.yml exec backend php artisan key:generate --force

# Installation command
install:
	cp .env.example .env
	make up-dev
	docker-compose -f docker-compose.dev.yml exec backend composer install
	make generate-key
	docker-compose -f docker-compose.dev.yml exec backend php artisan migrate:fresh --seed
	docker-compose -f docker-compose.dev.yml exec backend php artisan db:seed
	docker-compose -f docker-compose.dev.yml exec frontend npm install

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

clear-cache-dev:
	docker-compose -f docker-compose.dev.yml exec backend php artisan cache:clear
	docker-compose -f docker-compose.dev.yml exec backend php artisan config:clear
	docker-compose -f docker-compose.dev.yml exec backend php artisan route:clear
	docker-compose -f docker-compose.dev.yml exec backend php artisan view:clear
	docker-compose -f docker-compose.dev.yml exec backend composer dump-autoload

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

clear-cache-prod:
	docker-compose -f docker-compose.prod.yml exec backend php artisan cache:clear
	docker-compose -f docker-compose.prod.yml exec backend php artisan config:clear
	docker-compose -f docker-compose.prod.yml exec backend php artisan route:clear
	docker-compose -f docker-compose.prod.yml exec backend php artisan view:clear
	docker-compose -f docker-compose.prod.yml exec backend composer dump-autoload
	docker-compose -f docker-compose.prod.yml exec backend php artisan config:cache
	docker-compose -f docker-compose.prod.yml exec backend php artisan route:cache
	docker-compose -f docker-compose.prod.yml exec backend php artisan view:cache

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