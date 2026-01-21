<?php

namespace Deployer;

require 'recipe/symfony.php';

// ============================================================================
// CONFIGURATION GÉNÉRALE
// ============================================================================

set('application', 'symfony-app');
set('repository', 'git@github.com:VOTRE-USERNAME/VOTRE-REPO.git'); // À MODIFIER
set('git_tty', false); // Désactiver le TTY pour les serveurs mutualisés
set('keep_releases', 3); // Garder 3 releases pour rollback rapide

// Dossiers partagés entre les déploiements
set('shared_files', [
    '.env.local',
]);

set('shared_dirs', [
    'var/log',
    'var/sessions',
    'public/uploads',
]);

// Dossiers en écriture
set('writable_dirs', [
    'var',
    'var/cache',
    'var/log',
    'var/sessions',
    'public/uploads',
]);

set('writable_mode', 'chmod');
set('writable_chmod_mode', '0755');

// ============================================================================
// CONFIGURATION DU SERVEUR HOSTINGER (PRODUCTION)
// ============================================================================

host('production')
    ->setHostname('VOTRE-SERVEUR.hostinger.com') // À MODIFIER : ex: srv123456.hostinger.com
    ->setRemoteUser('u123456789') // À MODIFIER : votre username SSH
    ->setDeployPath('/home/u123456789/domains/votredomaine.com/public_html') // À MODIFIER
    ->setPort(65002) // Port SSH Hostinger (généralement 65002)
    ->set('branch', 'main')
    ->set('deploy_path', '/home/u123456789/domains/votredomaine.com') // À MODIFIER
    ->set('http_user', 'u123456789') // À MODIFIER
    ->set('writable_use_sudo', false); // Pas de sudo sur mutualisé

// ============================================================================
// TÂCHES PERSONNALISÉES
// ============================================================================

/**
 * Vérifier la version PHP sur le serveur
 */
task('deploy:check_php', function () {
    $phpVersion = run('php -v');
    writeln("📋 Version PHP sur le serveur : \n$phpVersion");
});

/**
 * Installation des dépendances Composer (production only)
 */
task('deploy:vendors', function () {
    run('cd {{release_path}} && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist');
});

/**
 * Exécuter les migrations Doctrine
 */
task('deploy:migrate', function () {
    run('cd {{release_path}} && php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration');
});

/**
 * Clear et warmup du cache Symfony
 */
task('deploy:cache', function () {
    run('cd {{release_path}} && php bin/console cache:clear --no-warmup');
    run('cd {{release_path}} && php bin/console cache:warmup');
});

/**
 * Créer le symlink du public_html vers current/public
 * (Important pour Hostinger où public_html est le document root)
 */
task('deploy:symlink_public', function () {
    $deployPath = get('deploy_path');

    // Supprimer l'ancien public_html s'il existe
    run("if [ -d $deployPath/public_html ] && [ ! -L $deployPath/public_html ]; then rm -rf $deployPath/public_html; fi");

    // Créer le symlink
    run("ln -sfn $deployPath/current/public $deployPath/public_html");

    writeln("✅ Symlink public_html → current/public créé");
});

/**
 * Mettre les bonnes permissions
 */
task('deploy:permissions', function () {
    run('chmod -R 755 {{release_path}}/var');
    run('chmod -R 755 {{release_path}}/public/uploads');

    writeln("✅ Permissions configurées");
});

// ============================================================================
// WORKFLOW DE DÉPLOIEMENT
// ============================================================================

desc('Déployer le projet Symfony sur Hostinger');
task('deploy', [
    'deploy:prepare',
    'deploy:check_php',
    'deploy:vendors',
    'deploy:cache',
    'deploy:migrate',
    'deploy:permissions',
    'deploy:publish',
    'deploy:symlink_public', // Important pour Hostinger
]);

// Après un déploiement réussi
after('deploy:failed', 'deploy:unlock');
after('deploy:success', function () {
    writeln("🎉 Déploiement terminé avec succès !");
});

// ============================================================================
// TÂCHES UTILITAIRES
// ============================================================================

/**
 * Rollback vers la release précédente
 */
desc('Rollback vers la release précédente');
task('rollback', function () {
    run('cd {{deploy_path}} && ln -sfn releases/$(ls -t releases | sed -n 2p) current');
    run('cd {{deploy_path}} && ln -sfn current/public public_html');

    writeln("⏪ Rollback effectué");
});

/**
 * Afficher les logs de production
 */
desc('Afficher les logs de production');
task('logs:prod', function () {
    run('tail -n 50 {{deploy_path}}/shared/var/log/prod.log');
});

/**
 * Vider le cache de production
 */
desc('Vider le cache de production');
task('cache:clear', function () {
    run('cd {{deploy_path}}/current && php bin/console cache:clear --env=prod');

    writeln("🧹 Cache vidé");
});
