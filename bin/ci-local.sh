#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

run_step() {
  local label="$1"
  shift
  echo ""
  echo "==> ${label}"
  "$@"
}

run_step "Validate composer.json" composer validate --strict
run_step "Install dependencies" composer install --prefer-dist --no-progress --no-interaction

run_step "PHP-CS-Fixer (dry-run)" vendor/bin/php-cs-fixer fix --dry-run --diff --verbose
run_step "TwigCS" vendor/bin/twig-cs-fixer lint templates/
run_step "No JS in Twig (chmod +x)" chmod +x bin/check-no-js-in-twig.sh
run_step "No JS in Twig" bash bin/check-no-js-in-twig.sh

run_step "Symfony cache:clear (test)" php bin/console cache:clear --env=test
run_step "Symfony cache:warmup (test)" php bin/console cache:warmup --env=test
run_step "PHPStan" vendor/bin/phpstan analyse --memory-limit=1G
run_step "Symfony requirements-checker" vendor/bin/requirements-checker

run_step "Lint YAML" php bin/console lint:yaml config --parse-tags
run_step "Lint Twig" php bin/console lint:twig templates
run_step "Lint Container" php bin/console lint:container

if [[ -z "${DATABASE_URL:-}" ]]; then
  echo ""
  echo "❌ DATABASE_URL non défini. Le pipeline CI l'utilise."
  echo "   Ex: export DATABASE_URL='mysql://root:root@127.0.0.1:3306/symfony_test'"
  exit 1
fi

run_step "Setup test database" php bin/console doctrine:database:create --env=test --if-not-exists
run_step "Setup test schema" php bin/console doctrine:schema:create --env=test
run_step "PHPUnit" vendor/bin/phpunit

echo ""
echo "✅ CI locale terminée."
