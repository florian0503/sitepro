# Symfony DevOps Architecture

Architecture professionnelle pour projet Symfony avec Docker, CI/CD et déploiement automatisé vers Hostinger.

## Stack Technique

- **Backend** : Symfony 6/7
- **Template Engine** : Twig
- **Base de données** : MySQL 8.0
- **Environnement Local** : Docker (Nginx + PHP-FPM + MySQL + Mailpit)
- **Production** : Hostinger Web Hosting (mutualisé)
- **CI/CD** : GitHub Actions
- **Déploiement** : Deployer

## Fonctionnalités

- Environnement de développement Docker identique pour tous les développeurs
- Pipeline CI/CD automatisé avec checks de qualité stricts
- Déploiement atomique zéro-downtime vers production
- Contrôle qualité strict : PSR-12, PHPStan, TwigCS
- Règle custom : interdiction de JavaScript dans les templates Twig

## Démarrage Rapide

### 1. Installation

```bash
# Cloner le repository
git clone <votre-repo>
cd webdesigner

# Installer les dépendances de qualité
composer require --dev \
    friendsofphp/php-cs-fixer \
    phpstan/phpstan \
    phpstan/phpstan-symfony \
    phpstan/extension-installer \
    vincentlanglet/twig-cs-fixer \
    symfony/requirements-checker \
    deployer/deployer

# Démarrer l'environnement Docker
make install
```

### 2. Accès aux services

- **Application** : http://localhost:8080
- **Mailpit** : http://localhost:8025
- **MySQL** : localhost:3306 (symfony/symfony)

### 3. Commandes principales

```bash
# Développement
make up              # Démarrer les containers
make down            # Arrêter les containers
make shell           # Entrer dans le container PHP
make logs            # Voir les logs

# Base de données
make db-create       # Créer la BDD
make db-migrate      # Exécuter les migrations
make db-reset        # Reset complet de la BDD

# Qualité du code
make quality         # Lancer tous les checks
make fix             # Corriger automatiquement le code
make test            # Lancer les tests

# Déploiement
make deploy          # Déployer en production
make rollback        # Rollback
```

## Workflow Git

```
feature/xxx → develop (Tests) → main (Déploiement automatique)
```

### Branches

- `main` : Production (déploiement automatique via CI/CD)
- `develop` : Pré-production / Tests
- `feature/*` : Développement de fonctionnalités

### Process de développement

```bash
# 1. Créer une branche feature
git checkout -b feature/ma-fonctionnalite

# 2. Développer et commiter
git add .
git commit -m "feat: ma nouvelle fonctionnalité"

# 3. Vérifier la qualité avant de push
make quality
make test

# 4. Pusher et créer une PR vers develop
git push origin feature/ma-fonctionnalite

# 5. Une fois la PR mergée dans develop, tester
# 6. Merger develop dans main pour déployer en prod
```

## Contrôle Qualité

Le projet applique des standards stricts :

### PHP-CS-Fixer (PSR-12)

```bash
# Vérifier
vendor/bin/php-cs-fixer fix --dry-run --diff

# Corriger
vendor/bin/php-cs-fixer fix
```

### PHPStan (Niveau 6)

```bash
vendor/bin/phpstan analyse
```

### TwigCS

```bash
# Vérifier
vendor/bin/twig-cs-fixer lint templates/

# Corriger
vendor/bin/twig-cs-fixer lint --fix templates/
```

### No JS in Twig (Règle Custom)

Interdiction stricte de balises `<script>` dans les fichiers `.html.twig`.

```bash
bash bin/check-no-js-in-twig.sh
```

Le JavaScript doit être dans des fichiers `.js` séparés et chargé via `asset()` ou `importmap`.

## Pipeline CI/CD

### Job 1 : Integration (Toutes les branches)

- Validation Composer
- Code Style (PHP-CS-Fixer)
- Syntaxe Twig (TwigCS)
- No JS in Twig (Script custom)
- Analyse Statique (PHPStan)
- Lint YAML/Twig/Container
- Tests (PHPUnit)

### Job 2 : Deployment (Branche main uniquement)

- Déploiement automatique vers Hostinger
- Déploiement atomique (zéro coupure)
- Migrations BDD automatiques
- Cache warming

## Déploiement

### Configuration

1. Modifier `deploy.php` :
   - Repository Git
   - Hostname Hostinger
   - Username SSH
   - Deploy Path

2. Configurer les secrets GitHub :
   - `SSH_PRIVATE_KEY`
   - `SSH_KNOWN_HOSTS`

Voir [GITHUB_SECRETS.md](GITHUB_SECRETS.md) pour plus de détails.

### Déploiement manuel

```bash
# Déployer
dep deploy production

# Rollback si problème
dep rollback production

# Voir les logs
dep logs:prod production
```

### Déploiement automatique

Le déploiement se fait automatiquement à chaque push sur `main` si tous les tests passent.

## Structure du Projet

```
webdesigner/
├── .github/
│   └── workflows/
│       └── pipeline.yml          # Pipeline CI/CD
├── bin/
│   └── check-no-js-in-twig.sh   # Script anti-JS
├── docker/
│   ├── nginx/                    # Config Nginx
│   └── php/                      # Config PHP-FPM
├── .php-cs-fixer.dist.php        # Config PHP-CS-Fixer
├── .twig-cs-fixer.dist.php       # Config TwigCS
├── phpstan.neon                  # Config PHPStan
├── docker-compose.yml            # Config Docker
├── deploy.php                    # Config Deployer
├── Makefile                      # Commandes utiles
├── SETUP.md                      # Guide d'installation
└── GITHUB_SECRETS.md             # Config des secrets
```

## Documentation

- [SETUP.md](SETUP.md) : Guide d'installation complet
- [GITHUB_SECRETS.md](GITHUB_SECRETS.md) : Configuration des secrets GitHub

## Équipe

- 2 développeurs
- Environnement local identique via Docker
- Standards de code stricts et automatisés

## Support

Pour toute question ou problème, consultez la section Troubleshooting dans [SETUP.md](SETUP.md).

## Licence

Projet privé
