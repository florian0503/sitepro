$ErrorActionPreference = "Stop"

function Run-Step {
    param(
        [string]$Label,
        [string]$Command
    )
    Write-Host ""
    Write-Host "==> $Label"
    Invoke-Expression $Command
}

Set-Location (Resolve-Path "$PSScriptRoot\..")

function Ensure-DockerMySql {
    if (-not (Get-Command docker-compose -ErrorAction SilentlyContinue)) {
        throw "docker-compose introuvable. Installe Docker Desktop ou ajoute docker-compose au PATH."
    }

    $mysqlId = (docker-compose ps -q mysql) 2>$null
    if (-not $mysqlId) {
        Write-Host ""
        Write-Host "==> Démarrage MySQL via docker-compose"
        docker-compose up -d mysql | Out-Host
        $mysqlId = (docker-compose ps -q mysql)
    } else {
        Write-Host ""
        Write-Host "==> MySQL Docker déjà lancé"
    }

    Write-Host "==> Attente MySQL healthy"
    $retries = 30
    while ($retries -gt 0) {
        $status = (docker inspect --format "{{.State.Health.Status}}" $mysqlId) 2>$null
        if ($status -eq "healthy") {
            Write-Host "✅ MySQL est healthy."
            return
        }
        Start-Sleep -Seconds 2
        $retries--
    }

    throw "MySQL n'est pas healthy après attente. Vérifie docker-compose logs mysql."
}

Ensure-DockerMySql

if (-not $env:DATABASE_URL) {
    $env:DATABASE_URL = "mysql://root:root@127.0.0.1:3306/symfony_test"
    Write-Host ""
    Write-Host "==> DATABASE_URL auto-définie: $env:DATABASE_URL"
}

Run-Step "Validate composer.json" "composer validate --strict"
Run-Step "Install dependencies (no-scripts)" "composer install --prefer-dist --no-progress --no-interaction --no-scripts"
Run-Step "Auto-scripts: cache:clear" "php bin/console cache:clear"
Run-Step "Auto-scripts: assets:install" "php bin/console assets:install public"
Run-Step "Auto-scripts: requirements-checker" "php vendor\bin\requirements-checker"
Run-Step "Auto-scripts: importmap:install" "php bin/console importmap:install"

Run-Step "PHP-CS-Fixer (dry-run)" "php vendor\bin\php-cs-fixer fix --dry-run --diff --verbose"
Run-Step "TwigCS" "php vendor\bin\twig-cs-fixer lint templates/"
Write-Host ""
Write-Host "==> No JS in Twig"
$violations = Get-ChildItem -Path "templates" -Recurse -Filter "*.html.twig" |
    Select-String -Pattern "<script"
if ($violations) {
    Write-Host ""
    Write-Host "❌ ERREUR : Balise <script> détectée dans les fichiers Twig !"
    Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    $violations | ForEach-Object {
        Write-Host ("{0}:{1}:{2}" -f $_.Path, $_.LineNumber, $_.Line.Trim())
    }
    Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    Write-Host ""
    Write-Host "📋 RÈGLE : Séparation des préoccupations"
    Write-Host "   Le JavaScript doit être dans des fichiers .js séparés,"
    Write-Host "   et chargé via asset() ou importmap."
    exit 1
} else {
    Write-Host "✅ Aucune violation détectée : Tous les templates sont conformes."
}

Run-Step "Symfony cache:clear (test)" "php bin/console cache:clear --env=test"
Run-Step "Symfony cache:warmup (test)" "php bin/console cache:warmup --env=test"
Run-Step "PHPStan" "php vendor\bin\phpstan analyse --memory-limit=1G"
Run-Step "Symfony requirements-checker" "php vendor\bin\requirements-checker"

Run-Step "Lint YAML" "php bin/console lint:yaml config --parse-tags"
Run-Step "Lint Twig" "php bin/console lint:twig templates"
Run-Step "Lint Container" "php bin/console lint:container"

if (-not $env:DATABASE_URL) {
    Write-Host ""
    Write-Host "❌ DATABASE_URL non défini. Le pipeline CI l'utilise."
    Write-Host "   Ex: `$env:DATABASE_URL='mysql://root:root@127.0.0.1:3306/symfony_test'"
    exit 1
}

Run-Step "Setup test database" "php bin/console doctrine:database:create --env=test --if-not-exists"
Run-Step "Setup test schema" "php bin/console doctrine:schema:create --env=test"
Run-Step "PHPUnit" "php vendor\bin\phpunit"

Write-Host ""
Write-Host "✅ CI locale terminée."
