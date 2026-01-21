# 📘 Guide d'Installation et de Configuration

## 📦 1. Installation des Dépendances Composer

Installez les outils de qualité en dépendances de développement :

```bash
composer require --dev \
    friendsofphp/php-cs-fixer \
    phpstan/phpstan \
    phpstan/phpstan-symfony \
    phpstan/extension-installer \
    vincentlanglet/twig-cs-fixer \
    symfony/requirements-checker
```

## 🐳 2. Configuration de l'Environnement Local (Docker)

### Démarrage de l'environnement

```bash
# Construire et démarrer les containers
docker-compose up -d --build

# Vérifier que tout fonctionne
docker-compose ps
```

### Accès aux services

- **Application Symfony** : http://localhost:8080
- **Mailpit (Interface email)** : http://localhost:8025
- **MySQL** : localhost:3306
  - User: `symfony`
  - Password: `symfony`
  - Database: `symfony`

### Commandes utiles Docker

```bash
# Entrer dans le container PHP
docker-compose exec php bash

# Voir les logs
docker-compose logs -f php

# Arrêter les containers
docker-compose down

# Arrêter ET supprimer les volumes (attention : données perdues)
docker-compose down -v
```

### Installation de Symfony dans le container

```bash
# Entrer dans le container
docker-compose exec php bash

# Installer les dépendances
composer install

# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

## ✅ 3. Vérification de la Qualité du Code

### PHP-CS-Fixer (Code Style)

```bash
# Vérifier le code (dry-run)
vendor/bin/php-cs-fixer fix --dry-run --diff

# Corriger automatiquement
vendor/bin/php-cs-fixer fix
```

### PHPStan (Analyse Statique)

```bash
# Analyser le code
vendor/bin/phpstan analyse

# Avec plus de détails
vendor/bin/phpstan analyse --memory-limit=1G -vvv
```

### TwigCS (Syntaxe Twig)

```bash
# Vérifier les templates Twig
vendor/bin/twig-cs-fixer lint templates/

# Corriger automatiquement
vendor/bin/twig-cs-fixer lint --fix templates/
```

### Script Custom : No JS in Twig

```bash
# Vérifier qu'aucune balise <script> n'est présente dans les Twig
bash bin/check-no-js-in-twig.sh
```

## 🚀 4. Configuration du Déploiement (Deployer)

### Installation de Deployer

```bash
# Installation globale (recommandé)
curl -LO https://deployer.org/deployer.phar
chmod +x deployer.phar
sudo mv deployer.phar /usr/local/bin/dep

# Ou via Composer
composer require --dev deployer/deployer
```

### Configuration du fichier deploy.php

Modifiez les valeurs suivantes dans `deploy.php` :

```php
// Ligne 15 : Votre repository Git
set('repository', 'git@github.com:VOTRE-USERNAME/VOTRE-REPO.git');

// Ligne 42 : Informations du serveur Hostinger
host('production')
    ->setHostname('srv123456.hostinger.com') // Votre serveur
    ->setRemoteUser('u123456789') // Votre username SSH
    ->setDeployPath('/home/u123456789/domains/votredomaine.com') // Chemin de déploiement
```

### Récupération des informations Hostinger

1. **Hostname SSH** : Visible dans votre panel Hostinger → Advanced → SSH Access
2. **Username** : Format `u123456789`
3. **Port SSH** : Généralement `65002`
4. **Deploy Path** : `/home/USERNAME/domains/votredomaine.com`

### Première connexion SSH

```bash
# Tester la connexion SSH
ssh u123456789@srv123456.hostinger.com -p 65002

# Une fois connecté, créer la structure
mkdir -p /home/u123456789/domains/votredomaine.com/{releases,shared}
mkdir -p /home/u123456789/domains/votredomaine.com/shared/{var/log,public/uploads}

# Créer le fichier .env.local en production
nano /home/u123456789/domains/votredomaine.com/shared/.env.local
```

Contenu du `.env.local` en production :

```env
APP_ENV=prod
APP_DEBUG=false
DATABASE_URL="mysql://db_user:db_password@localhost:3306/db_name?serverVersion=8.0"
APP_SECRET=VOTRE_SECRET_ICI
```

### Déploiement manuel

```bash
# Premier déploiement
dep deploy production

# Rollback si problème
dep rollback production

# Voir les logs
dep logs:prod production

# Vider le cache
dep cache:clear production
```

## 🔐 5. Configuration GitHub Actions (CI/CD)

### Secrets à configurer dans GitHub

Allez dans **Settings → Secrets and variables → Actions → New repository secret**

| Nom du Secret | Description | Exemple de valeur |
|---------------|-------------|-------------------|
| `SSH_PRIVATE_KEY` | Clé privée SSH pour se connecter au serveur | Contenu de `~/.ssh/id_rsa` |
| `SSH_KNOWN_HOSTS` | Fingerprint du serveur pour éviter les warnings SSH | Obtenu via `ssh-keyscan` |

### Génération des secrets

#### 1. SSH_PRIVATE_KEY

```bash
# Générer une paire de clés SSH (si vous n'en avez pas)
ssh-keygen -t rsa -b 4096 -C "deploy@github-actions" -f ~/.ssh/deploy_key

# Afficher la clé privée (à copier dans GitHub Secrets)
cat ~/.ssh/deploy_key

# Copier la clé publique sur le serveur Hostinger
ssh-copy-id -i ~/.ssh/deploy_key.pub -p 65002 u123456789@srv123456.hostinger.com

# OU manuellement :
# 1. Copier le contenu de ~/.ssh/deploy_key.pub
# 2. Se connecter en SSH au serveur
# 3. Ajouter dans ~/.ssh/authorized_keys
```

#### 2. SSH_KNOWN_HOSTS

```bash
# Récupérer le fingerprint du serveur
ssh-keyscan -p 65002 srv123456.hostinger.com

# Exemple de sortie (à copier dans GitHub Secrets) :
# srv123456.hostinger.com ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAAB...
```

### Workflow CI/CD

Le pipeline s'exécute automatiquement :

- **Sur toutes les branches** : Job Integration (tests, linting, analyse)
- **Sur la branche main uniquement** : Job Integration + Job Deployment

#### Forcer un déploiement

```bash
# Merger develop dans main
git checkout main
git merge develop
git push origin main

# Le déploiement se lancera automatiquement
```

## 🧪 6. Commandes Pratiques

### Développement Local

```bash
# Lancer tous les checks qualité d'un coup
vendor/bin/php-cs-fixer fix && \
vendor/bin/twig-cs-fixer lint --fix templates/ && \
bash bin/check-no-js-in-twig.sh && \
vendor/bin/phpstan analyse

# Lancer les tests
vendor/bin/phpunit

# Créer une migration
docker-compose exec php php bin/console make:migration

# Exécuter les migrations
docker-compose exec php php bin/console doctrine:migrations:migrate
```

### Production

```bash
# Vérifier l'état du déploiement
dep ssh production

# Voir les releases déployées
dep releases production

# Supprimer les anciennes releases
dep cleanup production
```

## 📂 7. Structure des Fichiers Générés

```
webdesigner/
├── .github/
│   └── workflows/
│       └── pipeline.yml          # Pipeline CI/CD
├── bin/
│   └── check-no-js-in-twig.sh   # Script custom anti-JS
├── docker/
│   ├── nginx/
│   │   ├── nginx.conf
│   │   └── default.conf
│   └── php/
│       ├── Dockerfile
│       └── php.ini
├── .php-cs-fixer.dist.php        # Config PHP-CS-Fixer
├── .twig-cs-fixer.dist.php       # Config TwigCS
├── phpstan.neon                  # Config PHPStan
├── docker-compose.yml            # Config Docker
└── deploy.php                    # Config Deployer
```

## 🔧 8. Troubleshooting

### Le container PHP ne démarre pas

```bash
# Vérifier les logs
docker-compose logs php

# Reconstruire les images
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Erreur de permissions dans Docker

```bash
# Corriger les permissions
docker-compose exec php chown -R symfony:symfony /var/www/symfony/var
```

### Déploiement échoue sur Hostinger

```bash
# Vérifier la connexion SSH
ssh -p 65002 u123456789@srv123456.hostinger.com

# Vérifier les permissions
ls -la /home/u123456789/domains/votredomaine.com

# Vérifier la version PHP sur le serveur
ssh -p 65002 u123456789@srv123456.hostinger.com "php -v"
```

### PHPStan échoue

```bash
# Générer le cache Symfony d'abord
php bin/console cache:clear --env=dev
php bin/console cache:warmup --env=dev

# Puis relancer PHPStan
vendor/bin/phpstan analyse
```

## 📚 9. Ressources

- [Documentation Symfony](https://symfony.com/doc/current/index.html)
- [Deployer Documentation](https://deployer.org/docs/7.x/getting-started)
- [PHP-CS-Fixer Rules](https://cs.symfony.com/)
- [PHPStan Levels](https://phpstan.org/user-guide/rule-levels)
- [GitHub Actions](https://docs.github.com/en/actions)
