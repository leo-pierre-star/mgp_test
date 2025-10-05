# Guide de Démarrage Rapide

## Installation et Configuration

### 1. Installer les dépendances

```bash
cd front
npm install
```

### 2. Configurer les variables d'environnement

Copiez le fichier `.env.example` en `.env` :

```bash
cp .env.example .env
```

Modifiez si nécessaire l'URL de l'API dans `.env`.

### 3. Lancer l'application

```bash
npm run dev
```

L'application sera accessible sur **http://localhost:5173**

## Utilisation

### Parcours Utilisateur

1. **Inscription** (`/register`)

   - Créez un compte avec un email et un mot de passe sécurisé
   - Le mot de passe doit contenir :
     - Minimum 8 caractères
     - Une majuscule
     - Un chiffre
     - Un caractère spécial
   - Validation en temps réel des critères

2. **Connexion** (`/login`)

   - Connectez-vous avec vos identifiants
   - Le token JWT est stocké automatiquement

3. **Mot de passe oublié** (`/request-password-reset`)

   - Entrez votre email
   - Recevez un code de réinitialisation par email
   - Utilisez ce code sur la page `/reset-password`

4. **Espace Client** (`/dashboard`)
   - Accessible uniquement après connexion
   - Formulaire de contact avec :
     - Sujet (obligatoire)
     - Message (obligatoire)
     - Pièce jointe (optionnelle, max 2 Mo)
   - Bouton de déconnexion dans le header

## API Backend

L'application communique avec l'API Symfony située dans le dossier `api/`.

### Endpoints utilisés

- `POST /api/register` - Inscription
- `POST /api/login` - Connexion
- `POST /api/request-password-reset` - Demande de réinitialisation
- `POST /api/reset-password` - Réinitialisation du mot de passe
- `POST /api/contact` - Envoi d'un message de contact

### Configuration CORS

Assurez-vous que le backend autorise les requêtes depuis `http://localhost:5173`.

## Technologies

- **Vue 3** (Composition API)
- **TypeScript**
- **Vue Router** (navigation)
- **Axios** (requêtes HTTP)
- **Tailwind CSS** (design)
- **Vite** (bundler)

## Structure des Routes

- `/` → Redirection vers `/login`
- `/login` → Page de connexion
- `/register` → Page d'inscription
- `/request-password-reset` → Demande de réinitialisation
- `/reset-password` → Réinitialisation du mot de passe
- `/dashboard` → Espace client (protégé)

## Sécurité

- **Routes protégées** : Le dashboard nécessite un token JWT
- **Redirection automatique** : Si non connecté, redirection vers `/login`
- **Déconnexion** : Supprime le token et redirige vers `/login`
- **Validation** : Vérification côté client du mot de passe et de la taille des fichiers

## Dépannage

### Le serveur ne démarre pas

Vérifiez que les dépendances sont installées :

```bash
npm install
```

### Les styles Tailwind ne s'appliquent pas

1. Vérifiez que `postcss.config.js` est présent
2. Redémarrez le serveur :

```bash
pkill -f vite
npm run dev
```

3. Videz le cache du navigateur (Cmd+Shift+R)

### Erreurs CORS

Vérifiez la configuration CORS dans le backend Symfony (`config/packages/nelmio_cors.yaml`).

### Les routes ne fonctionnent pas

Assurez-vous que le backend est démarré sur `http://localhost:8000`.

## Build Production

```bash
npm run build
```

Les fichiers de production seront dans le dossier `dist/`.
