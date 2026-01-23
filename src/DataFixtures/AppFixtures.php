<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Realisation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private SluggerInterface $slugger,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Create admin user
        $admin = new User();
        $admin->setEmail('admin@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin12'));
        $manager->persist($admin);

        // Create categories
        $categories = [];
        $categoryNames = ['Site Vitrine', 'E-commerce', 'Application Web', 'Landing Page'];

        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug($this->slugger->slug($name)->lower()->toString());
            $manager->persist($category);
            $categories[$name] = $category;
        }

        // Create sample realisations
        $realisations = [
            [
                'title' => 'Boulangerie Durand',
                'description' => 'Site vitrine élégant pour une boulangerie artisanale parisienne. Design chaleureux avec galerie photos des produits et formulaire de commande.',
                'category' => 'Site Vitrine',
                'clientName' => 'Boulangerie Durand',
                'url' => 'https://example.com/boulangerie-durand',
                'isPublished' => true,
            ],
            [
                'title' => 'Mode & Style Boutique',
                'description' => 'Boutique en ligne complète avec paiement sécurisé. Catalogue de plus de 500 produits, filtres avancés et gestion des stocks en temps réel.',
                'category' => 'E-commerce',
                'clientName' => 'Mode & Style',
                'url' => 'https://example.com/mode-style',
                'isPublished' => true,
            ],
            [
                'title' => 'Cabinet Avocat Martin',
                'description' => 'Site professionnel pour un cabinet d\'avocats. Présentation des services, équipe et prise de rendez-vous en ligne.',
                'category' => 'Site Vitrine',
                'clientName' => 'Cabinet Martin',
                'url' => 'https://example.com/cabinet-martin',
                'isPublished' => true,
            ],
            [
                'title' => 'Restaurant Le Gourmet',
                'description' => 'Site avec réservation en ligne et menu digital. Intégration avec le système de caisse du restaurant.',
                'category' => 'Site Vitrine',
                'clientName' => 'Le Gourmet',
                'url' => 'https://example.com/le-gourmet',
                'isPublished' => true,
            ],
            [
                'title' => 'Tech Solutions App',
                'description' => 'Application web de gestion de projet pour une startup tech. Dashboard personnalisé, gestion des tâches et rapports.',
                'category' => 'Application Web',
                'clientName' => 'Tech Solutions',
                'url' => 'https://example.com/tech-solutions',
                'isPublished' => true,
            ],
            [
                'title' => 'Lancement Produit XYZ',
                'description' => 'Landing page optimisée pour le lancement d\'un nouveau produit. A/B testing et intégration avec les outils marketing.',
                'category' => 'Landing Page',
                'clientName' => 'XYZ Corp',
                'url' => 'https://example.com/xyz-launch',
                'isPublished' => true,
            ],
        ];

        foreach ($realisations as $data) {
            $realisation = new Realisation();
            $realisation->setTitle($data['title']);
            $realisation->setDescription($data['description']);
            $realisation->setCategory($categories[$data['category']]);
            $realisation->setClientName($data['clientName']);
            $realisation->setUrl($data['url']);
            $realisation->setIsPublished($data['isPublished']);
            $manager->persist($realisation);
        }

        $manager->flush();
    }
}
