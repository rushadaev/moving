.PHONY: help install up down build rebuild restart logs clean migrate seed test

# Colors for terminal output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
RED := \033[0;31m
NC := \033[0m # No Color

help: ## Show this help message
	@echo '$(BLUE)Available commands:$(NC)'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2}'

# ============================================
# PRODUCTION COMMANDS
# ============================================

up: ## Start all services in production mode
	@echo '$(BLUE)Starting all services...$(NC)'
	docker-compose -f docker-compose.prod.yml up -d
	@echo '$(GREEN)All services started successfully!$(NC)'

down: ## Stop all services
	@echo '$(YELLOW)Stopping all services...$(NC)'
	docker-compose -f docker-compose.prod.yml down
	@echo '$(GREEN)All services stopped!$(NC)'

build: ## Build all Docker images
	@echo '$(BLUE)Building all Docker images...$(NC)'
	docker-compose -f docker-compose.prod.yml build
	@echo '$(GREEN)Build completed!$(NC)'

rebuild: ## Rebuild all services from scratch
	@echo '$(YELLOW)Rebuilding all services...$(NC)'
	docker-compose -f docker-compose.prod.yml down
	docker-compose -f docker-compose.prod.yml build --no-cache
	docker-compose -f docker-compose.prod.yml up -d
	@echo '$(GREEN)Rebuild completed!$(NC)'

restart: ## Restart all services
	@echo '$(BLUE)Restarting all services...$(NC)'
	docker-compose -f docker-compose.prod.yml restart
	@echo '$(GREEN)Services restarted!$(NC)'

logs: ## Show logs from all services
	docker-compose -f docker-compose.prod.yml logs -f

status: ## Show status of all services
	docker-compose -f docker-compose.prod.yml ps

# ============================================
# MOVING PROJECT COMMANDS
# ============================================

moving-logs: ## Show logs for moving project
	docker-compose -f docker-compose.prod.yml logs -f moving-frontend moving-backend moving-nginx

moving-backend-shell: ## Access moving backend shell
	docker-compose -f docker-compose.prod.yml exec moving-backend sh

moving-migrate: ## Run migrations for moving project
	docker-compose -f docker-compose.prod.yml exec moving-backend php artisan migrate --force

moving-seed: ## Run seeders for moving project
	docker-compose -f docker-compose.prod.yml exec moving-backend php artisan db:seed --force

moving-cache-clear: ## Clear cache for moving project
	docker-compose -f docker-compose.prod.yml exec moving-backend php artisan cache:clear
	docker-compose -f docker-compose.prod.yml exec moving-backend php artisan config:clear
	docker-compose -f docker-compose.prod.yml exec moving-backend php artisan route:clear
	docker-compose -f docker-compose.prod.yml exec moving-backend php artisan view:clear

# ============================================
# ALEXBUILDSERVICE PROJECT COMMANDS
# ============================================

alexbuild-logs: ## Show logs for alexbuildservice project
	docker-compose -f docker-compose.prod.yml logs -f alexbuild-frontend alexbuild-backend

alexbuild-backend-shell: ## Access alexbuildservice backend shell
	docker-compose -f docker-compose.prod.yml exec alexbuild-backend sh

alexbuild-migrate: ## Run migrations for alexbuildservice project
	docker-compose -f docker-compose.prod.yml exec alexbuild-backend php artisan migrate --force

alexbuild-seed: ## Run seeders for alexbuildservice project
	docker-compose -f docker-compose.prod.yml exec alexbuild-backend php artisan db:seed --force

alexbuild-cache-clear: ## Clear cache for alexbuildservice project
	docker-compose -f docker-compose.prod.yml exec alexbuild-backend php artisan cache:clear
	docker-compose -f docker-compose.prod.yml exec alexbuild-backend php artisan config:clear
	docker-compose -f docker-compose.prod.yml exec alexbuild-backend php artisan route:clear
	docker-compose -f docker-compose.prod.yml exec alexbuild-backend php artisan view:clear

# ============================================
# DATABASE COMMANDS
# ============================================

db-moving: ## Access moving MySQL database
	docker-compose -f docker-compose.prod.yml exec moving-mysql mysql -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE)

db-alexbuild: ## Access alexbuildservice MySQL database
	docker-compose -f docker-compose.prod.yml exec alexbuild-mysql mysql -ualexbuild -palexbuild_secret alexbuildservice

# ============================================
# NGINX COMMANDS
# ============================================

nginx-reload: ## Reload nginx configuration
	docker-compose -f docker-compose.prod.yml exec nginx nginx -s reload
	@echo '$(GREEN)Nginx configuration reloaded!$(NC)'

nginx-test: ## Test nginx configuration
	docker-compose -f docker-compose.prod.yml exec nginx nginx -t

nginx-logs: ## Show nginx logs
	docker-compose -f docker-compose.prod.yml logs -f nginx

# ============================================
# UTILITY COMMANDS
# ============================================

clean: ## Remove all containers, volumes, and images
	@echo '$(RED)WARNING: This will remove all containers, volumes, and images!$(NC)'
	@read -p "Are you sure? [y/N] " -n 1 -r; \
	echo; \
	if [[ $$REPLY =~ ^[Yy]$$ ]]; then \
		docker-compose -f docker-compose.prod.yml down -v; \
		docker system prune -af; \
		echo '$(GREEN)Cleanup completed!$(NC)'; \
	fi

install: ## Initial installation and setup
	@echo '$(BLUE)Running initial installation...$(NC)'
	docker-compose -f docker-compose.prod.yml up -d
	@echo '$(YELLOW)Waiting for services to start...$(NC)'
	sleep 10
	docker-compose -f docker-compose.prod.yml exec moving-backend php artisan migrate --force
	docker-compose -f docker-compose.prod.yml exec moving-backend php artisan db:seed --force
	docker-compose -f docker-compose.prod.yml exec alexbuild-backend php artisan migrate --force
	docker-compose -f docker-compose.prod.yml exec alexbuild-backend php artisan db:seed --force
	@echo '$(GREEN)Installation completed!$(NC)'

health-check: ## Check health of all services
	@echo '$(BLUE)Checking service health...$(NC)'
	@curl -s -o /dev/null -w "Moving site: %{http_code}\n" https://mooweemoving.com || echo "Moving site: DOWN"
	@curl -s -o /dev/null -w "AlexBuild site: %{http_code}\n" https://alexbuildservice.net || echo "AlexBuild site: DOWN"
