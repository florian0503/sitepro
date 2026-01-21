.PHONY: help install up down restart logs shell db-create db-migrate quality fix test deploy

# Couleurs pour l'output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
RED := \033[0;31m
RESET := \033[0m

## —— Makefile Symfony + Docker ——————————————————————————————————————————————

help: ## Affiche cette aide
	@echo "$(BLUE)Commandes disponibles :$(RESET)"
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "$(GREEN)%-20s$(RESET) %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker ——————————————————————————————————————————————————————————————————

install: ## Installe le projet (build + composer install)
	@echo "$(BLUE)🚀 Installation du projet...$(RESET)"
	docker-compose up -d --build
	docker-compose exec php composer install
	@echo "$(GREEN)✅ Installation terminée !$(RESET)"

up: ## Démarre les containers Docker
	@echo "$(BLUE)🐳 Démarrage des containers...$(RESET)"
	docker-compose up -d
	@echo "$(GREEN)✅ Containers démarrés !$(RESET)"

down: ## Arrête les containers Docker
	@echo "$(YELLOW)🛑 Arrêt des containers...$(RESET)"
	docker-compose down

restart: down up ## Redémarre les containers

logs: ## Affiche les logs des containers
	docker-compose logs -f

shell: ## Entre dans le container PHP
	docker-compose exec php bash

## —— Base de Données ————————————————————————————————————————————————————————

db-create: ## Crée la base de données
	docker-compose exec php php bin/console doctrine:database:create --if-not-exists

db-migrate: ## Exécute les migrations
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction

db-reset: ## Reset complet de la BDD (DROP + CREATE + MIGRATE)
	docker-compose exec php php bin/console doctrine:database:drop --force --if-exists
	docker-compose exec php php bin/console doctrine:database:create
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction

## —— Qualité du Code ————————————————————————————————————————————————————————

quality: ## Lance tous les checks qualité
	@echo "$(BLUE)🔍 Vérification de la qualité du code...$(RESET)"
	@echo "$(YELLOW)→ PHP-CS-Fixer...$(RESET)"
	vendor/bin/php-cs-fixer fix --dry-run --diff
	@echo "$(YELLOW)→ TwigCS...$(RESET)"
	vendor/bin/twig-cs-fixer lint templates/
	@echo "$(YELLOW)→ No JS in Twig...$(RESET)"
	bash bin/check-no-js-in-twig.sh
	@echo "$(YELLOW)→ PHPStan...$(RESET)"
	vendor/bin/phpstan analyse
	@echo "$(GREEN)✅ Tous les checks sont passés !$(RESET)"

fix: ## Corrige automatiquement le code (PHP-CS-Fixer + TwigCS)
	@echo "$(BLUE)🔧 Correction automatique du code...$(RESET)"
	vendor/bin/php-cs-fixer fix
	vendor/bin/twig-cs-fixer lint --fix templates/
	@echo "$(GREEN)✅ Code corrigé !$(RESET)"

phpstan: ## Lance PHPStan uniquement
	docker-compose exec php vendor/bin/phpstan analyse

## —— Tests ——————————————————————————————————————————————————————————————————

test: ## Lance les tests PHPUnit
	@echo "$(BLUE)🧪 Exécution des tests...$(RESET)"
	docker-compose exec php vendor/bin/phpunit
	@echo "$(GREEN)✅ Tests terminés !$(RESET)"

## —— Déploiement ————————————————————————————————————————————————————————————

deploy: ## Déploie en production via Deployer
	@echo "$(RED)🚀 Déploiement en production...$(RESET)"
	dep deploy production
	@echo "$(GREEN)✅ Déploiement terminé !$(RESET)"

rollback: ## Rollback du dernier déploiement
	@echo "$(YELLOW)⏪ Rollback en cours...$(RESET)"
	dep rollback production
	@echo "$(GREEN)✅ Rollback terminé !$(RESET)"

## —— Cache ——————————————————————————————————————————————————————————————————

cache-clear: ## Vide le cache Symfony
	docker-compose exec php php bin/console cache:clear

cache-warmup: ## Warmup du cache Symfony
	docker-compose exec php php bin/console cache:warmup
