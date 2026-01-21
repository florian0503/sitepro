<?php

namespace Deployer;

require 'recipe/symfony.php';

// ============================================================================
// CONFIGURATION GÉNÉRALE
// ============================================================================

set('application', 'symfony-app');

// URL de ton dépôt GitHub
set('repository', 'git@github.com:florian0503/sitepro.git');

set('git_tty', false);
set('keep_releases', 3);

// Dossiers partagés (logs, images, .env)
set('shared_files', ['.env.local']);
set('shared_dirs', ['var/log', 'var/sessions', 'public/uploads']);
set('writable_dirs', ['var', 'var/cache', 'var/log', 'var/sessions', 'public/uploads']);

// ============================================================================
// CONFIGURATION SERVEUR (Via Secrets GitHub)
// ============================================================================

host('production')
    ->set('hostname', getenv('HOST')) // Lit le secret HOST
    ->set('remote_user', getenv('USER')) // Lit le secret USER
    ->set('port', getenv('PORT')) // Lit le secret PORT

    // CORRECTION ICI : Chemin propre sans https, avec un sous-dossier /application
    ->set('deploy_path', '~/domains/blue-swan-296877.hostingersite.com/application')

    ->set('http_user', getenv('USER'))
    ->set('writable_mode', 'chmod')
    ->set('ssh_multiplexing', false); // Plus stable sur mutualisé

// ============================================================================
// TÂCHES SPÉCIFIQUES HOSTINGER
// ============================================================================

task('deploy:vendors', function () {
    run('cd {{release_path}} && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist');
});

task('deploy:migrate', function () {
    run('cd {{release_path}} && php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration');
});

task('deploy:cache', function () {
    run('cd {{release_path}} && php bin/console cache:clear --no-warmup');
    run('cd {{release_path}} && php bin/console cache:warmup');
});

// Le lien symbolique magique pour Hostinger
task('deploy:symlink_public', function () {
    // CORRECTION ICI : Chemin du domaine racine
    $domainPath = '~/domains/blue-swan-296877.hostingersite.com';

    // On supprime le dossier public_html par défaut s'il existe (et n'est pas déjà un lien)
    run("if [ -d $domainPath/public_html ] && [ ! -L $domainPath/public_html ]; then rm -rf $domainPath/public_html; fi");

    // On crée le lien vers la version déployée
    run("ln -sfn {{deploy_path}}/current/public $domainPath/public_html");

    writeln('✅ Symlink public_html créé avec succès');
});

// ============================================================================
// ORCHESTRATION DU DÉPLOIEMENT
// ============================================================================

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:cache',
    'deploy:migrate',
    'deploy:publish',
    'deploy:symlink_public', // On relie le site à la fin
]);

after('deploy:failed', 'deploy:unlock');
