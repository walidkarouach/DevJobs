# DevJobs API

Projet pédagogique — API REST développée avec **Laravel 12** et **Laravel Sanctum**, simulant une plateforme de mise en relation entre **candidats** et **entreprises** autour d'offres d'emploi.

---

## Sommaire

- [Présentation](#présentation)
- [Fonctionnalités](#fonctionnalités)
- [Structure du projet](#structure-du-projet)
- [Installation](#installation)
- [Configuration](#configuration)
- [Migrations](#migrations)
- [Seeders](#seeders)
- [Authentification (Sanctum)](#authentification-sanctum)
- [Rôles & permissions](#rôles--permissions)
- [Liste des endpoints](#liste-des-endpoints)
- [Exemples Postman](#exemples-postman)

---

## Présentation

DevJobs est une API REST permettant à :

- des **entreprises** de publier des offres d'emploi et de gérer les candidatures reçues,
- des **candidats** de consulter les offres, postuler et suivre l'état de leurs candidatures,
- un **admin** de superviser l'ensemble de la plateforme (compétences, statistiques...).

L'authentification est gérée par **Laravel Sanctum** (tokens API). Le rôle de chaque utilisateur (`admin`, `entreprise`, `candidate`) est stocké directement dans la colonne `role` de la table `users` — aucune table `roles` séparée n'est utilisée, dans un souci de simplicité.

## Fonctionnalités

- Inscription / connexion / déconnexion via Sanctum
- Gestion des profils **Entreprise** (CRUD)
- Gestion des **Compétences** (CRUD, réservé à l'admin)
- Gestion des **Offres** (CRUD), liées à une entreprise et à des compétences (relation many-to-many)
- **Candidatures** : postuler à une offre, consulter ses candidatures, accepter/refuser une candidature
- **Recherche d'offres** par titre, entreprise et/ou compétence (filtres combinables)
- **Dashboard admin** avec statistiques globales de la plateforme
- Seeders + Factories pour générer un jeu de données de test cohérent

## Structure du projet

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php          # register / login / logout
│   │   ├── EntrepriseController.php    # CRUD entreprise
│   │   ├── CompetenceController.php    # CRUD compétence (admin)
│   │   ├── OffreController.php         # CRUD offre + recherche
│   │   ├── CandidatureController.php   # postuler / consulter / changer statut
│   │   └── AdminController.php         # statistiques admin
│   ├── Middleware/
│   │   └── RoleMiddleware.php          # middleware "role:xxx"
│   └── Requests/                       # FormRequests (validation)
├── Models/
│   ├── User.php
│   ├── Entreprise.php
│   ├── Offre.php
│   ├── Competence.php
│   └── Candidature.php
database/
├── migrations/
├── factories/
└── seeders/
routes/
└── api.php                             # toutes les routes de l'API
```

## Installation

Prérequis : PHP >= 8.2, Composer.

```bash
# 1. Cloner / se placer dans le projet
cd DevJobs

# 2. Installer les dépendances
composer install

# 3. Copier le fichier d'environnement
cp .env.example .env

# 4. Générer la clé d'application
php artisan key:generate

# 5. Créer la base de données SQLite (si elle n'existe pas déjà)
touch database/database.sqlite

# 6. Lancer les migrations
php artisan migrate

# 7. (Optionnel mais recommandé) Peupler la base avec des données de test
php artisan db:seed

# 8. Démarrer le serveur de développement
php artisan serve
```

L'API est alors disponible sur `http://127.0.0.1:8000/api`.

## Configuration

Le projet utilise **SQLite** par défaut (`DB_CONNECTION=sqlite` dans `.env`), ce qui évite d'avoir à configurer un serveur MySQL/PostgreSQL pour un usage pédagogique. Vous pouvez bien sûr basculer vers MySQL en adaptant les variables `DB_*` du `.env`.

## Migrations

Les migrations créent notamment :

| Table | Description |
|---|---|
| `users` | Utilisateurs (avec colonne `role`: `admin`, `entreprise`, `candidate`) |
| `entreprises` | Profils entreprise (liés à un `user`) |
| `competences` | Référentiel de compétences |
| `offres` | Offres d'emploi (liées à une `entreprise`) |
| `candidatures` | Candidatures (`user_id`, `offre_id`, `statut`), contrainte unique `(user_id, offre_id)` |
| `competence_offre` | Table pivot Many-to-Many entre `offres` et `competences` |
| `competence_user` | Table pivot Many-to-Many entre `users` et `competences` |
| `personal_access_tokens` | Tokens Sanctum |

```bash
php artisan migrate          # exécuter les migrations
php artisan migrate:fresh    # tout recréer depuis zéro
```

## Seeders

```bash
php artisan db:seed
# ou pour repartir de zéro avec des données fraîches :
php artisan migrate:fresh --seed
```

Le `DatabaseSeeder` exécute, dans l'ordre :

1. **UserSeeder** — 15 candidats (`role: candidate`)
2. **CompetenceSeeder** — 10 compétences (PHP, Laravel, React, Docker...)
3. **EntrepriseSeeder** — 10 entreprises (chacune liée à un nouvel utilisateur `role: entreprise`)
4. **OffreSeeder** — 30 offres réparties sur les entreprises existantes, chacune avec 2 à 4 compétences attachées
5. **CandidatureSeeder** — jusqu'à 50 candidatures générées à partir des candidats et des offres existants (en respectant la contrainte d'unicité)
6. **AdminSeeder** — un compte admin

Compte admin créé automatiquement :

```
email: admin@devjobs.com
password: admin123
```

## Authentification (Sanctum)

L'authentification se fait par token. Après un `register` ou un `login`, l'API renvoie un `token` à transmettre dans l'en-tête `Authorization` de chaque requête protégée :

```
Authorization: Bearer {token}
```

## Rôles & permissions

| Action | candidate | entreprise | admin |
|---|:---:|:---:|:---:|
| Créer / gérer son profil entreprise | ❌ | ✅ (le sien) | ✅ (tous) |
| Gérer les compétences | ❌ | ❌ | ✅ |
| Créer / gérer ses offres | ❌ | ✅ (les siennes) | ✅ (toutes) |
| Postuler à une offre | ✅ | ❌ | ❌ |
| Consulter ses candidatures envoyées | ✅ | — | — |
| Consulter les candidatures reçues sur ses offres | — | ✅ | ✅ (toutes) |
| Accepter / refuser une candidature | ❌ | ✅ (les siennes) | ✅ |
| Consulter le dashboard statistiques | ❌ | ❌ | ✅ |

## Liste des endpoints

### Authentification

| Méthode | URL | Accès | Description |
|---|---|---|---|
| POST | `/api/register` | public | Créer un compte (`candidate` ou `entreprise`) |
| POST | `/api/login` | public | Connexion, retourne un token |
| POST | `/api/logout` | authentifié | Déconnexion (révoque le token courant) |

### Entreprises

| Méthode | URL | Accès | Description |
|---|---|---|---|
| GET | `/api/entreprises` | authentifié | Liste des entreprises |
| GET | `/api/entreprises/{id}` | authentifié | Détail d'une entreprise |
| POST | `/api/entreprises` | entreprise, admin | Créer son profil entreprise |
| PUT | `/api/entreprises/{id}` | entreprise (propriétaire), admin | Modifier une entreprise |
| DELETE | `/api/entreprises/{id}` | entreprise (propriétaire), admin | Supprimer une entreprise |

### Compétences

| Méthode | URL | Accès | Description |
|---|---|---|---|
| GET | `/api/competences` | admin | Liste des compétences |
| POST | `/api/competences` | admin | Créer une compétence |
| GET | `/api/competences/{id}` | admin | Détail d'une compétence |
| PUT | `/api/competences/{id}` | admin | Modifier une compétence |
| DELETE | `/api/competences/{id}` | admin | Supprimer une compétence |

### Offres

| Méthode | URL | Accès | Description |
|---|---|---|---|
| GET | `/api/offres` | authentifié | Liste des offres (avec entreprise et compétences) |
| GET | `/api/offres/{id}` | authentifié | Détail d'une offre |
| POST | `/api/offres` | entreprise, admin | Créer une offre (champ `competences`: tableau d'IDs, optionnel) |
| PUT | `/api/offres/{id}` | entreprise (propriétaire), admin | Modifier une offre |
| DELETE | `/api/offres/{id}` | entreprise (propriétaire), admin | Supprimer une offre |
| GET | `/api/search/offres` | authentifié | Recherche par `titre`, `entreprise`, `competence` (combinables) |

### Candidatures

| Méthode | URL | Accès | Description |
|---|---|---|---|
| POST | `/api/offres/{id}/candidatures` | candidate | Postuler à une offre |
| GET | `/api/candidatures` | authentifié | Liste des candidatures (filtrée selon le rôle) |
| PUT | `/api/candidatures/{id}/statut` | entreprise (propriétaire de l'offre), admin | Accepter / refuser une candidature |

### Admin

| Méthode | URL | Accès | Description |
|---|---|---|---|
| GET | `/api/admin/statistiques` | admin | Statistiques globales de la plateforme |

## Exemples Postman

> Base URL utilisée dans les exemples : `http://127.0.0.1:8000/api`
> Pensez à ajouter l'en-tête `Accept: application/json` sur toutes les requêtes.

### 1. Register (candidat)

```
POST /api/register
Content-Type: application/json

{
  "name": "Alice Candidat",
  "email": "alice@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "candidate"
}
```

### 2. Register (entreprise)

```
POST /api/register
Content-Type: application/json

{
  "name": "Bob Recruteur",
  "email": "bob@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "entreprise"
}
```

### 3. Login

```
POST /api/login
Content-Type: application/json

{
  "email": "alice@example.com",
  "password": "password123"
}
```

Réponse : récupérez le champ `token` et utilisez-le dans l'en-tête `Authorization: Bearer {token}` pour toutes les requêtes suivantes.

### 4. Logout

```
POST /api/logout
Authorization: Bearer {token}
```

### 5. Créer une entreprise (compte "entreprise")

```
POST /api/entreprises
Authorization: Bearer {token_entreprise}
Content-Type: application/json

{
  "nom": "OpenAI",
  "secteur": "Informatique",
  "description": "Entreprise spécialisée en intelligence artificielle.",
  "logo": "openai.png"
}
```

### 6. Créer une compétence (admin)

```
POST /api/competences
Authorization: Bearer {token_admin}
Content-Type: application/json

{
  "nom": "Laravel"
}
```

### 7. Créer une offre (compte "entreprise")

```
POST /api/offres
Authorization: Bearer {token_entreprise}
Content-Type: application/json

{
  "titre": "Développeur Laravel",
  "description": "Développement et maintenance d'API REST.",
  "type_contrat": "CDI",
  "competences": [1, 2]
}
```

### 8. Modifier une offre

```
PUT /api/offres/1
Authorization: Bearer {token_entreprise}
Content-Type: application/json

{
  "titre": "Développeur Laravel Senior",
  "description": "Développement et maintenance d'API REST.",
  "type_contrat": "CDI",
  "competences": [1, 3]
}
```

### 9. Supprimer une offre

```
DELETE /api/offres/1
Authorization: Bearer {token_entreprise}
```

### 10. Postuler à une offre (compte "candidate")

```
POST /api/offres/1/candidatures
Authorization: Bearer {token_candidate}
```

### 11. Consulter mes candidatures

```
GET /api/candidatures
Authorization: Bearer {token}
```

(Le résultat dépend du rôle : un candidat voit ses candidatures envoyées, une entreprise voit celles reçues sur ses offres, l'admin voit tout.)

### 12. Changer le statut d'une candidature (entreprise ou admin)

```
PUT /api/candidatures/1/statut
Authorization: Bearer {token_entreprise}
Content-Type: application/json

{
  "statut": "acceptee"
}
```

### 13. Recherche d'offres

```
GET /api/search/offres?titre=Laravel&entreprise=OpenAI&competence=Docker
Authorization: Bearer {token}
```

### 14. Dashboard admin

```
GET /api/admin/statistiques
Authorization: Bearer {token_admin}
```

Réponse type :

```json
{
  "utilisateurs": 27,
  "entreprises": 10,
  "offres": 30,
  "competences": 10,
  "candidatures": {
    "total": 50,
    "en_attente": 18,
    "acceptees": 16,
    "refusees": 16
  }
}
```
