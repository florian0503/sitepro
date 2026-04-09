<?php

declare(strict_types=1);

namespace App\Config;

final class DevisGrid
{
    /**
     * @return array<int, array{name: string, items: array<int, array{name: string, description: string, price: float, isMonthly: bool}>}>
     */
    public static function getCategories(): array
    {
        return [
            [
                'name' => 'Comptes & Espace Membre',
                'items' => [
                    [
                        'name' => 'Création de compte classique',
                        'description' => 'Inscription, email, mot de passe et système de connexion/déconnexion sécurisé.',
                        'price' => 150.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Social Login',
                        'description' => 'Connexion rapide en un clic via Google, Apple, Facebook.',
                        'price' => 50.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Espace Client complet',
                        'description' => 'Tableau de bord dédié : modification profil, historique d\'actions, téléchargement documents.',
                        'price' => 100.0,
                        'isMonthly' => false,
                    ],
                ],
            ],
            [
                'name' => 'Réservation & Agenda',
                'items' => [
                    [
                        'name' => 'Calendrier interactif',
                        'description' => 'Sélection date/heure et notification automatique de demande de réservation.',
                        'price' => 250.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Gestion des créneaux avancée',
                        'description' => 'Jours de fermeture, temps de pause entre RDV, disponibilités en temps réel.',
                        'price' => 100.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Synchronisation externe',
                        'description' => 'Liaison avec Google Calendar / Outlook pour éviter les doublons.',
                        'price' => 50.0,
                        'isMonthly' => false,
                    ],
                ],
            ],
            [
                'name' => 'E-commerce & Paiement',
                'items' => [
                    [
                        'name' => 'Module de paiement sécurisé',
                        'description' => 'Intégration Stripe ou PayPal pour la vente d\'un service ou produit unique.',
                        'price' => 250.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Catalogue de produits',
                        'description' => 'Fiches produits, catégories, tags et mise en avant d\'articles à la une.',
                        'price' => 400.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Gestion de stock automatisée',
                        'description' => 'Décrémentation auto des stocks, alertes de rupture, blocage ventes si indisponible.',
                        'price' => 200.0,
                        'isMonthly' => false,
                    ],
                ],
            ],
            [
                'name' => 'Logistique & Envois',
                'items' => [
                    [
                        'name' => 'Frais de port simples',
                        'description' => 'Configuration frais fixes ou livraison gratuite au-delà d\'un certain montant.',
                        'price' => 100.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Frais de port dynamiques',
                        'description' => 'Calcul automatique selon le poids ou dimensions des articles.',
                        'price' => 100.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Intégration API Transporteur',
                        'description' => 'Connexion Colissimo / Mondial Relay : étiquettes et suivi en temps réel.',
                        'price' => 100.0,
                        'isMonthly' => false,
                    ],
                ],
            ],
            [
                'name' => 'Design & Expérience Utilisateur',
                'items' => [
                    [
                        'name' => 'Mode Sombre (Dark Mode)',
                        'description' => 'Commutateur clair/sombre avec adaptation complète de la palette de couleurs.',
                        'price' => 200.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Recherche avancée instantanée',
                        'description' => 'Barre de recherche dynamique sans rechargement, filtres personnalisés.',
                        'price' => 300.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Animations sur mesure',
                        'description' => 'Effets visuels fluides au défilement pour un rendu haut de gamme.',
                        'price' => 200.0,
                        'isMonthly' => false,
                    ],
                ],
            ],
            [
                'name' => 'Acquisition & Fidélisation',
                'items' => [
                    [
                        'name' => 'Module Chatbox / Live Chat',
                        'description' => 'Bulle de discussion interactive pour répondre aux visiteurs en temps réel.',
                        'price' => 150.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Système de Newsletter',
                        'description' => 'Formulaire de capture d\'emails RGPD + connexion Brevo / Mailchimp.',
                        'price' => 150.0,
                        'isMonthly' => false,
                    ],
                ],
            ],
            [
                'name' => 'Communication Automatisée',
                'items' => [
                    [
                        'name' => 'Configuration Mailer (emails transactionnels)',
                        'description' => 'Connexion SMTP pro + modèles visuels (bienvenue, confirmation, mot de passe oublié).',
                        'price' => 100.0,
                        'isMonthly' => false,
                    ],
                    [
                        'name' => 'Maintenance Mailer',
                        'description' => 'Frais mensuels du service d\'envoi pour le bon fonctionnement des communications.',
                        'price' => 10.0,
                        'isMonthly' => true,
                    ],
                ],
            ],
            [
                'name' => 'Internationalisation & Géolocalisation',
                'items' => [
                    [
                        'name' => 'Site Multilingue',
                        'description' => 'Architecture technique pour une langue supplémentaire (routage, interface de traduction). Traduction non incluse.',
                        'price' => 200.0,
                        'isMonthly' => false,
                    ],
                ],
            ],
        ];
    }
}
