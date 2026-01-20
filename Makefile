# @Developer Rouzot Julien copyright 2026 Agence Webnet.fr

CONSOLE = docker compose exec api-php php bin/console

## —— Symfony / Doctrine ——
migration: ## Génère une nouvelle migration (diff)
	$(CONSOLE) make:migration

migrate: ## Exécute les migrations en attente
	$(CONSOLE) doctrine:migrations:migrate --no-interaction

db-init: migration migrate ## Raccourci pour générer ET appliquer les migrations