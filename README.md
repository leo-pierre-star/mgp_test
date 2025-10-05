# MGP Test - Application Full Stack

> Stack moderne avec API Symfony et Frontend Vue 3 + TypeScript

## 🏗️ Architecture

- **Backend**: Symfony 7.3 + PostgreSQL 17
  - Authentification JWT (Lexik)
  - Reset de mot de passe par email
  - Documentation API OpenAPI (Nelmio)
  - Système de contact avec pièces jointes
- **Frontend**: Vue 3 + Vite + TypeScript + Tailwind CSS
  - Animations subtiles et UI moderne
  - Gestion de fichiers
  - ESLint & Prettier configurés
- **Infrastructure**: Docker Compose
  - Environnement de développement complet
  - MailHog pour les emails de test
  - Hot-reload pour l'API et le front

## 🚀 Démarrage rapide

### Prérequis

- Docker & Docker Compose
- (Optionnel) Node 20+ pour développement local

### Lancement

```bash
# Démarrer tous les services
docker compose -f api/compose.yaml up -d --build

# Appliquer les migrations de la base de données
docker compose -f api/compose.yaml exec api php bin/console doctrine:migrations:migrate --no-interaction
```

### Accès aux services

| Service           | URL                           | Description                                       |
| ----------------- | ----------------------------- | ------------------------------------------------- |
| 🌐 **Frontend**   | http://localhost:3000         | Interface utilisateur Vue 3                       |
| 🔧 **API**        | http://localhost:8000         | API REST Symfony                                  |
| 📚 **API Docs**   | http://localhost:8000/api/doc | Documentation OpenAPI interactive                 |
| 📧 **MailHog**    | http://localhost:8025         | Interface de test des emails                      |
| 🗄️ **PostgreSQL** | localhost:5432                | Base de données (user: mgp / pass: mgp / db: mgp) |

## 📁 Structure du projet

```
mgp_test/
├── api/                      # Backend Symfony
│   ├── compose.yaml          # Configuration Docker Compose
│   ├── compose.override.yaml # Surcharge locale (optionnel)
│   ├── config/               # Configuration Symfony
│   ├── src/                  # Code source
│   │   ├── Controller/       # Contrôleurs API
│   │   ├── Entity/          # Entités Doctrine
│   │   ├── Service/         # Services métier
│   │   └── Repository/      # Repositories
│   └── migrations/          # Migrations de base de données
│
└── front/                   # Frontend Vue 3
    ├── Dockerfile           # Image Docker pour le frontend
    ├── src/
    │   ├── views/          # Pages Vue
    │   ├── components/     # Composants réutilisables
    │   ├── services/       # Services (API client)
    │   └── router/         # Configuration Vue Router
    └── package.json
```

## 🔐 Authentification

Le système utilise JWT (JSON Web Tokens) pour l'authentification.

### Flux d'authentification

1. **Inscription** : `POST /api/users`

   ```json
   {
     "email": "user@example.com",
     "password": "Password123!"
   }
   ```

2. **Connexion** : `POST /api/login_check`

   ```json
   {
     "email": "user@example.com",
     "password": "Password123!"
   }
   ```

   Retourne un token JWT à utiliser dans les requêtes suivantes.

3. **Routes protégées** : Ajouter le header
   ```
   Authorization: Bearer <votre_token_jwt>
   ```

### Reset de mot de passe

1. `POST /api/password/forgot` avec l'email
2. Consulter l'email dans MailHog (http://localhost:8025)
3. Cliquer sur le lien ou copier le token
4. `POST /api/password/reset` avec le token et le nouveau mot de passe

## 🛠️ Commandes utiles

### Backend (API)

```bash
# Accéder au conteneur API
docker compose -f api/compose.yaml exec api bash

# Migrations
docker compose -f api/compose.yaml exec api php bin/console doctrine:migrations:migrate

# Créer une migration
docker compose -f api/compose.yaml exec api php bin/console make:migration

# Vider le cache
docker compose -f api/compose.yaml exec api php bin/console cache:clear

# Compter les utilisateurs connectés (commande custom)
docker compose -f api/compose.yaml exec api php bin/console app:count-connected-users

# Logs en temps réel
docker compose -f api/compose.yaml logs -f api
```

### Frontend

```bash
# Installer les dépendances (si développement local)
cd front && npm install

# Lancer le dev server localement
npm run dev

# Linter le code
npm run lint

# Formater le code avec Prettier
npm run format

# Build de production
npm run build

# Logs du conteneur frontend
docker compose -f api/compose.yaml logs -f front
```

### Docker

```bash
# Arrêter tous les services
docker compose -f api/compose.yaml down

# Arrêter et supprimer les volumes (⚠️ supprime la base de données)
docker compose -f api/compose.yaml down -v

# Reconstruire les images
docker compose -f api/compose.yaml up -d --build

# Voir les conteneurs actifs
docker compose -f api/compose.yaml ps

# Voir l'utilisation des ressources
docker stats
```

## ⚙️ Configuration

### Variables d'environnement

**Backend** (service `api`):

- `DATABASE_URL`: Connexion PostgreSQL (auto-configuré via Docker)
- `MAILER_DSN`: smtp://mailhog:1025
- `FRONT_RESET_BASE_URL`: http://localhost:5173/reset-password
- `JWT_SECRET_KEY`, `JWT_PUBLIC_KEY`: Clés JWT (dans `config/jwt/`)

**Frontend** (service `front`):

- `VITE_API_URL`: http://localhost:8000/api

### CORS

La configuration CORS est dans `api/config/packages/nelmio_cors.yaml` et autorise :

- http://localhost:3000
- http://localhost:5173

## 🧪 Développement

### Code Quality (Frontend)

Le projet utilise ESLint et Prettier pour maintenir la qualité du code :

```bash
# Vérifier les erreurs de linting
npm run lint

# Formater automatiquement le code
npm run format
```

**Configuration** :

- `.eslintrc.cjs` : Règles ESLint avec support Vue 3 + TypeScript
- `.prettierrc.json` : Règles de formatage du code
- Les deux outils sont configurés pour fonctionner ensemble

### Animations UI

Le frontend inclut un système d'animations subtiles :

- Animations d'entrée (card entrance, fade in, slide in)
- Feedback utilisateur (shake sur erreur, pulse sur succès)
- Transitions fluides sur les interactions
- Spinners de chargement animés

## 📦 Production

### Points d'attention pour le déploiement

1. **API** :

   - ❌ Ne pas utiliser `php -S` (serveur interne PHP)
   - ✅ Utiliser Nginx + PHP-FPM ou Apache
   - Configurer les variables d'environnement réelles
   - Générer de nouvelles clés JWT sécurisées
   - Configurer un vrai service d'emailing (SendGrid, Mailgun, etc.)

2. **Frontend** :

   - Builder le projet : `npm run build`
   - Servir les fichiers statiques via CDN ou Nginx
   - Mettre à jour `VITE_API_URL` vers l'URL de production

3. **Base de données** :

   - Utiliser un service PostgreSQL managé
   - Sauvegardes automatiques
   - Réplication si nécessaire

4. **Sécurité** :
   - HTTPS obligatoire
   - Ajuster les règles CORS pour les domaines de production uniquement
   - Variables d'environnement sécurisées (secrets, vault)
   - Rate limiting sur les endpoints sensibles

## 🐛 Dépannage

### Le frontend ne se connecte pas à l'API

- Vérifier que tous les conteneurs sont démarrés : `docker compose -f api/compose.yaml ps`
- Vérifier les logs : `docker compose -f api/compose.yaml logs api`
- Vérifier la configuration CORS dans `api/config/packages/nelmio_cors.yaml`

### Erreur "relation user does not exist"

Appliquer les migrations :

```bash
docker compose -f api/compose.yaml exec api php bin/console doctrine:migrations:migrate
```

### Le conteneur frontend redémarre en boucle

Vérifier les logs :

```bash
docker compose -f api/compose.yaml logs front
```

Souvent lié à une erreur dans `package.json` ou dépendances manquantes.

## 📝 Fonctionnalités

- ✅ Inscription / Connexion utilisateur
- ✅ Authentification JWT
- ✅ Reset de mot de passe par email
- ✅ Formulaire de contact avec pièces jointes (max 2 Mo)
- ✅ Tracking des événements de connexion
- ✅ Validation des mots de passe (force, confirmation)
- ✅ Rate limiting sur le formulaire de contact
- ✅ Interface moderne avec animations
- ✅ Documentation API interactive

## 📄 Licence

Projet de test - Usage libre

---

**Développé avec** ❤️ **et** ☕

## Nettoyage

Arrêter et supprimer containers/volumes:

```bash
docker compose -f api/compose.yaml -f api/compose.override.yaml down -v
```

---

Tout problème: vérifier les logs `docker compose logs -f api`.
