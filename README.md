# Guide d'Installation du LMS test Laravel

Ce document fournit les instructions détaillées pour installer et configurer le Système de Gestion d'Apprentissage (LMS) modulaire basé sur le modèle Majestic Monolith de Laravel.


## Prérequis

Avant de commencer l'installation, assurez-vous que votre environnement dispose des éléments suivants :

- PHP 8.2 ou supérieur
- Composer 2.5+
- Node.js 20.x et npm 10.x
- MySQL 8.0+ ou PostgreSQL 14+
- Redis 7.0+
- Git
- Serveur web (Nginx ou Apache)
- Serveur Keycloak 21+ (pour l'authentification SSO)

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/benalisouhail12/lms-test.git
cd lms-test
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Installer les dépendances JavaScript

```bash
npm install
npm run build
```

### 4. Configurer les variables d'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Editez le fichier `.env` et configurez les paramètres suivants :

```
# Configuration de la base de données
DB_CONNECTION=mysql    # ou postgresql
DB_HOST=127.0.0.1
DB_PORT=3306          # ou 5432 pour PostgreSQL
DB_DATABASE=lms_db
DB_USERNAME=lms_user
DB_PASSWORD=votre_mot_de_passe_securise

# Configuration Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Configuration Keycloak
KEYCLOAK_BASE_URL=https://keycloak.votre-domaine.com
KEYCLOAK_REALM=lms-realm
KEYCLOAK_CLIENT_ID=lms-client
KEYCLOAK_CLIENT_SECRET=votre_client_secret
KEYCLOAK_REDIRECT_URI=https://votre-lms.com/auth/callback

# Configuration WebSocket
WEBSOCKET_HOST=127.0.0.1
WEBSOCKET_PORT=6001
WEBSOCKET_SSL=false
```



### 5. Exécuter les migrations 

```bash
php artisan migrate

```


### Journaux d'erreurs

Les journaux du LMS se trouvent dans :

```
/chemin/vers/lms-test/storage/logs/laravel.log
```

Pour les problèmes liés au serveur web, consultez :

- Nginx : `/var/log/nginx/error.log`
- Apache : `/var/log/apache2/lms-error.log`
