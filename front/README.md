# Application Espace Client - Frontend

Application Vue 3 + TypeScript + Tailwind CSS pour la gestion d'un espace client.

## Fonctionnalités

### Authentification

- **Inscription** : Création de compte avec validation dynamique du mot de passe
  - 8 caractères minimum
  - Une majuscule
  - Un chiffre
  - Un caractère spécial
- **Connexion** : Authentification par email et mot de passe
- **Mot de passe oublié** : Demande de réinitialisation par email
- **Réinitialisation** : Définition d'un nouveau mot de passe avec un code reçu par email
- **Déconnexion** : Suppression du token d'authentification

### Espace Client

- **Formulaire de contact** avec :
  - Sujet (requis)
  - Message (requis)
  - Pièce jointe (optionnel, max 2 Mo)
  - Formats acceptés : images, PDF, Word, texte

## Technologies

- **Vue 3** avec Composition API et `<script setup>`
- **TypeScript** pour le typage
- **Vue Router** pour la navigation
- **Axios** pour les appels API
- **Tailwind CSS** pour le design
- **Vite** pour le build

## Installation

```bash
npm install
```

## Configuration

Créez un fichier `.env` à la racine avec :

```
VITE_API_URL=http://localhost:8000/api
```

## Lancement

```bash
npm run dev
```

L'application sera accessible sur `http://localhost:5173`

## Structure

```
src/
├── views/          # Pages de l'application
│   ├── Login.vue
│   ├── Register.vue
│   ├── RequestPasswordReset.vue
│   ├── ResetPassword.vue
│   └── Dashboard.vue
├── router/         # Configuration des routes
│   └── index.ts
├── services/       # Services API
│   └── api.ts
├── types/          # Types TypeScript
│   └── index.ts
├── App.vue         # Composant racine
└── main.ts         # Point d'entrée
```

## Routes

- `/` - Redirection vers `/login`
- `/login` - Page de connexion
- `/register` - Page d'inscription
- `/request-password-reset` - Demande de réinitialisation
- `/reset-password` - Réinitialisation du mot de passe
- `/dashboard` - Espace client (protégé)

## Sécurité

- Routes protégées par middleware d'authentification
- Token JWT stocké dans localStorage
- Validation côté client des mots de passe
- Limitation de taille des fichiers (2 Mo max)
- Intercepteurs Axios pour gérer l'authentification et les erreurs

## Build

```bash
npm run build
```

## Palette de couleurs

Le projet utilise des couleurs personnalisées définies dans `tailwind.config.js` :

- **mgp** : Couleurs principales (primary, accent, secondary, highlight, light, dark)
- **forest** : Tons verts foncés
- **moss** : Tons verts moyens
- **earth** : Tons terre
- **tiger** : Tons orangés
- **sand** : Tons beiges/jaunes
