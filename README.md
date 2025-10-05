# MGP Test - Stack API (Symfony) + Front (Vue 3 + Vite)

## Aperçu

- Backend: Symfony 7.3 (JWT Auth, Postgres, Mailhog, NelmioDoc)
- Frontend: Vue 3 + Vite + TypeScript + Tailwind
- Dev env: Docker Compose

## Pré-requis

- Docker / Docker Compose
- (Optionnel) Node 20+ installé localement pour exécuter le front hors Docker

## Démarrage rapide

```bash
docker compose -f api/compose.yaml -f api/compose.override.yaml up --build
```

Accès:

- API: http://localhost:8000
- Documentation OpenAPI: http://localhost:8000/api/doc
- Front: http://localhost:3000
- Mailhog UI: http://localhost:8025
- Postgres: localhost:5432 (user/pass/db: mgp)

## Services (docker)

| Service  | Rôle                     | Port local     |
| -------- | ------------------------ | -------------- |
| api      | Symfony PHP server (dev) | 8000           |
| database | PostgreSQL 17 Alpine     | 5432           |
| mailhog  | Capture emails SMTP      | 8025 (HTTP UI) |
| front    | Vite dev server          | 3000           |

## Variables importantes

Backend (service `api`):

- MAILER_DSN=smtp://mailhog:1025
- FRONT_RESET_BASE_URL=http://localhost:3000/reset-password (pour les liens d'email)
- CORS: géré via `config/packages/nelmio_cors.yaml` (localhost:3000 autorisé)

Frontend (service `front`):

- VITE_API_BASE_URL=http://localhost:8000/api (injection dans build)

Adapter au besoin en prod (ne pas utiliser le serveur PHP interne ni Vite dev server).

## JWT

Clés présentes dans `api/config/jwt` (private/public). Ne pas commiter des clés réelles en prod.

## Commandes utiles

Migrer la base:

```bash
docker compose -f api/compose.yaml exec api php bin/console doctrine:migrations:migrate --no-interaction
```

Créer un utilisateur test:

```bash
docker compose -f api/compose.yaml exec api php bin/console app:create-user email=test@example.com password=Password123!
```

## Flux Auth

1. POST /api/login_check (JSON: email, password) -> token JWT
2. Utiliser `Authorization: Bearer <token>` sur les routes protégées

## Reset mot de passe

1. POST /api/password/forgot (email)
2. Email envoyé (visible dans Mailhog)
3. Front consomme le lien basé sur FRONT_RESET_BASE_URL
4. POST /api/password/reset (token + newPassword)

## Développement front hors Docker (optionnel)

```
cd front
npm install
npm run dev
```

Mettre `VITE_API_BASE_URL=http://localhost:8000/api` dans un fichier `.env` local si nécessaire.

## Tests (à ajouter)

Ajouter PHPUnit + config si besoin.

## Production (pistes)

- Remplacer serveur interne PHP par nginx + php-fpm
- Builder front (`npm run build`) et servir via CDN / nginx
- Ajuster CORS et URLs publiques
- Utiliser un provider d'email réel

## Nettoyage

Arrêter et supprimer containers/volumes:

```bash
docker compose -f api/compose.yaml -f api/compose.override.yaml down -v
```

---

Tout problème: vérifier les logs `docker compose logs -f api`.
