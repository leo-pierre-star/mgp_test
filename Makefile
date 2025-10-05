.PHONY: help start stop restart build logs clean migrate install lint format test

COMPOSE_FILE=api/compose.yaml
COMPOSE=docker compose -f $(COMPOSE_FILE)

GREEN=\033[0;32m
YELLOW=\033[1;33m
NC=\033[0m 

help:
	@echo "$(GREEN)MGP Test - Commandes disponibles$(NC)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(YELLOW)%-15s$(NC) %s\n", $$1, $$2}'
	@echo ""

start:
	@echo "$(GREEN)🚀 Démarrage des services...$(NC)"
	$(COMPOSE) up -d --build
	@echo "$(GREEN)✅ Services démarrés !$(NC)"
	@echo "  - Frontend: http://localhost:3000"
	@echo "  - API: http://localhost:8000"
	@echo "  - API Docs: http://localhost:8000/api/doc"
	@echo "  - MailHog: http://localhost:8025"

stop:
	@echo "$(YELLOW)⏸️  Arrêt des services...$(NC)"
	$(COMPOSE) down

restart:
	@echo "$(YELLOW)🔄 Redémarrage...$(NC)"
	$(COMPOSE) down
	$(COMPOSE) up -d --build

build:
	@echo "$(GREEN)🔨 Reconstruction des images...$(NC)"
	$(COMPOSE) build --no-cache

logs:
	$(COMPOSE) logs -f

logs-api: 
	$(COMPOSE) logs -f api

logs-front:
	$(COMPOSE) logs -f front

status: 
	$(COMPOSE) ps

migrate:
	@echo "$(GREEN)🗄️  Application des migrations...$(NC)"
	$(COMPOSE) exec api php bin/console doctrine:migrations:migrate --no-interaction

migration-create:
	$(COMPOSE) exec api php bin/console make:migration

db-reset:
	@echo "$(YELLOW)⚠️  Réinitialisation de la base de données...$(NC)"
	$(COMPOSE) exec api php bin/console doctrine:database:drop --force --if-exists
	$(COMPOSE) exec api php bin/console doctrine:database:create
	$(COMPOSE) exec api php bin/console doctrine:migrations:migrate --no-interaction

install: 
	@echo "$(GREEN)📦 Installation des dépendances...$(NC)"
	$(COMPOSE) exec api composer install
	$(COMPOSE) exec front npm install

cache-clear:
	$(COMPOSE) exec api php bin/console cache:clear

shell-api:
	$(COMPOSE) exec api bash

shell-front:	
	$(COMPOSE) exec front sh

shell-db:
	$(COMPOSE) exec database psql -U mgp -d mgp

lint:
	@echo "$(GREEN)🔍 Linting du code frontend...$(NC)"
	cd front && npm run lint

format:
	@echo "$(GREEN)✨ Formatage du code frontend...$(NC)"
	cd front && npm run format

lint-fix:
	cd front && npm run lint -- --fix

test: 
	@echo "$(YELLOW)⚠️  Tests non encore configurés$(NC)"

clean:
	@echo "$(YELLOW)🧹 Nettoyage complet...$(NC)"
	$(COMPOSE) down -v
	@echo "$(GREEN)✅ Nettoyage terminé$(NC)"

prune:
	@echo "$(YELLOW)🗑️  Nettoyage Docker...$(NC)"
	docker system prune -af --volumes

stats:
	docker stats

top:
	$(COMPOSE) top

count-users:
	$(COMPOSE) exec api php bin/console app:count-connected-users

.DEFAULT_GOAL := help
