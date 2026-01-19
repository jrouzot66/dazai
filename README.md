1. Démarrer les conteneurs :
   ```bash
   docker compose up -d --build
   ```

2. Installer les dépendances PHP (uniquement la première fois ou si le composer.json change) :
   ```bash
   docker compose exec api-php composer install
   ```