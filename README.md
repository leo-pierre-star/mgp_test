# MGP Test - Application Full Stack

> Stack moderne : Symfony 7.3 (API) + Vue 3 (Frontend) + PostgreSQL + Docker

## 🚀 Démarrage rapide

### Prérequis

- Docker & Docker Compose
- (Optionnel) Make pour les commandes simplifiées

### Installation

```bash
# Avec Make (recommandé)
make start        # Démarre tous les services
make migrate      # Applique les migrations

# Sans Make
docker compose -f api/compose.yaml up -d --build
docker compose -f api/compose.yaml exec api php bin/console doctrine:migrations:migrate --no-interaction
```

### Accès

| Service     | URL                           |
| ----------- | ----------------------------- |
| 🌐 Frontend | http://localhost:3000         |
| 🔧 API      | http://localhost:8000         |
| 📚 API Docs | http://localhost:8000/api/doc |
| 📧 MailHog  | http://localhost:8025         |
| 🗄️ Database | localhost:5432 (mgp/mgp/mgp)  |

## 🏗️ Architecture

**Backend (Symfony 7.3)**

- Authentification JWT (Lexik)
- Reset de mot de passe par email
- Documentation OpenAPI (Nelmio)
- Système de contact avec pièces jointes

**Frontend (Vue 3 + TypeScript)**

- Vite + Tailwind CSS + Animations subtiles
- ESLint & Prettier configurés
- Gestion de fichiers et validation

**Infrastructure**

- Docker Compose (API, Frontend, PostgreSQL, MailHog)
- Hot-reload automatique

## �️ Commandes Make

Le projet inclut un `Makefile` pour simplifier les opérations courantes :

```bash
make help           # Liste toutes les commandes disponibles
make start          # Démarre tous les services
make stop           # Arrête les services
make restart        # Redémarre les services
make logs           # Affiche les logs en temps réel
make migrate        # Applique les migrations
make shell-api      # Accède au shell du conteneur API
make shell-front    # Accède au shell du conteneur Frontend
make lint           # Lint le code frontend (ESLint)
make format         # Formate le code frontend (Prettier)
make clean          # Arrête et supprime les volumes
```

**Sans Make** : Utiliser `docker compose -f api/compose.yaml <command>`

## � Authentification & API

**Flux JWT** : Inscription (`POST /api/users`) → Connexion (`POST /api/login_check`) → Token JWT

**Routes protégées** : Header `Authorization: Bearer <token>`

**Reset mot de passe** :

1. `POST /api/password/forgot`
2. Email visible dans MailHog
3. `POST /api/password/reset` avec le token

**Documentation API** : http://localhost:8000/api/doc (OpenAPI)

## ⚙️ Configuration & Développement

**Code Quality** :

- ESLint + Prettier configurés (frontend)
- `make lint` et `make format` pour vérifier/formater le code
- Voir `CONTRIBUTING.md` pour les conventions

## 📝 Fonctionnalités

✅ Auth JWT • Reset password • Contact avec pièces jointes • Tracking connexions • Animations UI • Documentation API

**Développé avec** ❤️ **et** ☕
