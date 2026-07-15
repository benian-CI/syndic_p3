# syndic_p3

Application Laravel pour la gestion d'un syndic de copropriété.

## Installation locale

1. Copier le fichier d'exemple :
```powershell
copy .env.example .env
```
2. Installer les dépendances :
```powershell
composer install
npm ci
```
3. Générer la clé d'application :
```powershell
php artisan key:generate
```
4. Construire les assets :
```powershell
npm run build
```

## Déploiement sur Plesk

### Avant de pousser
- Ne pousse pas le fichier `.env`.
- Assure-toi que `.gitignore` contient bien `.env`, `vendor/`, `node_modules/`, `storage/`, `public/build/`.

### Sur Plesk
1. Crée le site ou domaine.
2. Place le code source dans le dossier du site.
3. Configure les variables d'environnement dans Plesk :
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://ton-domaine`
   - `DB_CONNECTION=mysql`
   - `DB_HOST=...`
   - `DB_PORT=3306`
   - `DB_DATABASE=...`
   - `DB_USERNAME=...`
   - `DB_PASSWORD=...`
   - `SESSION_SECURE_COOKIE=true`
   - `SESSION_HTTP_ONLY=true`
   - `SESSION_SAME_SITE=lax`
4. Installe les dépendances et compile les assets (CSS/JS) :
```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```
5. Exécute les commandes Laravel :
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

⚠️ **Important** : `public/build/` est ignoré par git (voir `.gitignore`). À chaque déploiement d'une nouvelle version, il faut refaire `npm run build` sur le serveur, sinon Plesk continue de servir l'ancien CSS/JS déjà présent — même si le code source (`resources/`) a bien été mis à jour. Si la page semble "cassée" ou affiche un ancien design après un déploiement, c'est presque toujours ça : relance `npm run build` puis vide le cache du navigateur.

## Notes de sécurité
- `APP_DEBUG` doit être `false` en production.
- `APP_KEY` doit rester secret.
- Ne jamais committer `.env` ni de secrets en clair.

## Commandes utiles
```bash
php artisan serve
npm run dev
php artisan test
```
