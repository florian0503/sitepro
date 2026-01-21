<?php

namespace Deployer;

require 'recipe/symfony.php';

// ============================================================================
// CONFIGURATION GENERALE
// ============================================================================

set('application', 'symfony-app');
set('repository', 'git@github.com:florian0503/sitepro.git');
set('git_tty', false);
set('keep_releases', 3);

// Dossiers partages
set('shared_files', ['.env.local']);
set('shared_dirs', ['var/log', 'var/sessions', 'public/uploads']);
set('writable_dirs', ['var', 'var/cache', 'var/log', 'var/sessions', 'public/uploads']);

// ============================================================================
// CONFIGURATION SERVEUR - Les placeholders sont remplaces par le CI
// ============================================================================

host('__DEPLOY_HOST__')
    ->set('remote_user', '__DEPLOY_USER__')
    ->set('port', __DEPLOY_PORT__)
    ->set('deploy_path', '~/domains/blue-swan-296877.hostingersite.com/application')
    ->set('http_user', '__DEPLOY_USER__')
    ->set('writable_mode', 'chmod')
    ->set('ssh_multiplexing', false);

// ============================================================================
// TACHES PERSONNALISEES
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

// Symlink public_html vers le dossier public de Symfony
task('deploy:symlink_public', function () {
    $domainPath = '~/domains/blue-swan-296877.hostingersite.com';
    run("if [ -d $domainPath/public_html ] && [ ! -L $domainPath/public_html ]; then rm -rf $domainPath/public_html; fi");
    run("ln -sfn {{deploy_path}}/current/public $domainPath/public_html");
    writeln('Symlink public_html created');
});

// ============================================================================
// DEPLOIEMENT
// ============================================================================

after('deploy:symlink', 'deploy:symlink_public');
after('deploy:failed', 'deploy:unlock');
