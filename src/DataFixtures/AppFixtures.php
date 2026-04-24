<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
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
        $admin->setEmail('contact@entryweb.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $_ENV['ADMIN_PASSWORD'] ?? 'ChangeMe!'));
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
            [
                'title' => 'Agence Immobilière Prestige',
                'description' => 'Site vitrine avec recherche avancée de biens immobiliers, visites virtuelles 360° et estimation en ligne.',
                'category' => 'Site Vitrine',
                'clientName' => 'Prestige Immo',
                'url' => 'https://example.com/prestige-immo',
                'isPublished' => true,
            ],
            [
                'title' => 'Fitness Store Pro',
                'description' => 'Boutique e-commerce spécialisée fitness avec abonnements, programmes personnalisés et vente d\'équipements.',
                'category' => 'E-commerce',
                'clientName' => 'Fitness Store',
                'url' => 'https://example.com/fitness-store',
                'isPublished' => true,
            ],
            [
                'title' => 'Dashboard Analytics',
                'description' => 'Application de visualisation de données avec graphiques interactifs, exports PDF et alertes automatisées.',
                'category' => 'Application Web',
                'clientName' => 'DataViz Inc',
                'url' => 'https://example.com/dataviz',
                'isPublished' => true,
            ],
            [
                'title' => 'Spa & Bien-être Zen',
                'description' => 'Site élégant pour un spa avec réservation de soins en ligne, cartes cadeaux et programme fidélité.',
                'category' => 'Site Vitrine',
                'clientName' => 'Spa Zen',
                'url' => 'https://example.com/spa-zen',
                'isPublished' => true,
            ],
            [
                'title' => 'Crypto Trading Platform',
                'description' => 'Landing page de conversion pour une plateforme de trading. Design moderne avec animations et formulaire optimisé.',
                'category' => 'Landing Page',
                'clientName' => 'CryptoTrade',
                'url' => 'https://example.com/cryptotrade',
                'isPublished' => true,
            ],
            [
                'title' => 'École de Musique Harmonie',
                'description' => 'Site avec inscription aux cours en ligne, planning interactif et espace élèves avec ressources pédagogiques.',
                'category' => 'Application Web',
                'clientName' => 'École Harmonie',
                'url' => 'https://example.com/ecole-harmonie',
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

        // Create sample articles
        $articles = [
            [
                'title' => 'Comment créer un site web qui convertit vraiment en 2025',
                'category' => 'Conseils',
                'excerpt' => 'Un beau site ne suffit pas. Découvrez les 5 leviers essentiels pour transformer vos visiteurs en clients dès les premières semaines.',
                'content' => "Un site web beau ne suffit pas. Ce qui fait la différence, c'est la conversion : transformer un visiteur anonyme en prospect, puis en client.\n\n**1. Un message clair au-dessus de la ligne de flottaison**\n\nLes 5 premières secondes sont décisives. Votre visiteur doit comprendre immédiatement ce que vous faites, pour qui, et pourquoi vous êtes la bonne option. Aucune ambiguïté.\n\n**2. Un appel à l'action unique et visible**\n\nTrop de choix tue le choix. Un seul CTA principal par page, bien contrasté, avec un verbe d'action clair : \"Demandez votre devis gratuit\", \"Réservez votre appel découverte\".\n\n**3. La preuve sociale avant tout**\n\nAvis clients, logos de références, études de cas chiffrées. La preuve sociale rassure et lève les objections avant même que le prospect ne les formule.\n\n**4. La vitesse de chargement**\n\nChaque seconde de chargement supplémentaire réduit les conversions de 7%. Google PageSpeed Score > 90, c'est non négociable.\n\n**5. Le mobile d'abord**\n\nPlus de 60% du trafic est mobile. Si votre site n'est pas parfait sur smartphone, vous perdez la majorité de vos visiteurs.",
                'coverImage' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&q=80',
                'readTime' => 6,
                'publishedAt' => new \DateTimeImmutable('-5 days'),
            ],
            [
                'title' => 'SEO local : dominez Google Maps pour votre ville en 3 mois',
                'category' => 'Tutoriel',
                'excerpt' => 'Le référencement local est la mine d\'or des petites entreprises. Voici la méthode exacte pour apparaître en tête des recherches locales.',
                'content' => "Le SEO local est l'opportunité la plus sous-exploitée pour les petites entreprises. Contrairement au SEO national, la concurrence y est souvent faible et les résultats rapides.\n\n**Étape 1 : Optimisez votre fiche Google Business Profile**\n\nC'est la base absolue. Remplissez 100% des champs, ajoutez des photos de qualité chaque semaine, répondez à tous les avis (même négatifs), et publiez des posts régulièrement.\n\n**Étape 2 : Cohérence NAP partout**\n\nVotre Nom, Adresse, et numéro de Téléphone doivent être identiques partout : site web, Pages Jaunes, Facebook, LinkedIn. Toute incohérence pénalise votre classement.\n\n**Étape 3 : Les avis clients, votre carburant**\n\nVisez 4,5+ étoiles avec minimum 50 avis. Mettez en place un système automatique de demande d'avis après chaque prestation.\n\n**Étape 4 : Contenu localisé**\n\nCréez des pages spécifiques à chaque ville que vous ciblez. \"Agence web à Lyon\", \"Création de site à Villeurbanne\" : Google comprend la géographie.\n\n**Étape 5 : Backlinks locaux**\n\nObtenez des liens depuis des sites locaux : chambre de commerce, associations professionnelles, annuaires locaux. 10 liens locaux valent mieux que 100 liens génériques.",
                'coverImage' => 'https://images.unsplash.com/photo-1432888622747-4eb9a8f2c293?w=1200&q=80',
                'readTime' => 8,
                'publishedAt' => new \DateTimeImmutable('-12 days'),
            ],
            [
                'title' => 'Pourquoi votre site fait fuir vos clients (et comment y remédier)',
                'category' => 'Conseils',
                'excerpt' => 'Un taux de rebond élevé est un signal d\'alarme. Ces 7 erreurs courantes sabotent silencieusement votre présence en ligne.',
                'content' => "Votre site reçoit du trafic mais personne ne prend contact ? Ce n'est pas un problème de visibilité, c'est un problème d'expérience utilisateur.\n\n**Erreur 1 : Trop de texte, trop dense**\n\nPersonne ne lit sur internet, on scanne. Utilisez des titres courts, des listes à puces, des paragraphes de 2-3 lignes maximum.\n\n**Erreur 2 : Des visuels de mauvaise qualité**\n\nUne photo floue ou pixelisée détruit instantanément votre crédibilité. Investissez dans de bonnes photos ou utilisez des banques d'images premium.\n\n**Erreur 3 : Un menu trop complexe**\n\nPlus de 7 items dans votre navigation = confusion garantie. Simplifiez radicalement.\n\n**Erreur 4 : Aucune preuve de confiance**\n\nSans SIRET visible, sans adresse physique, sans avis clients, vous êtes perçu comme suspect.\n\n**Erreur 5 : Un formulaire trop long**\n\nChaque champ supplémentaire réduit les conversions. Demandez uniquement ce dont vous avez vraiment besoin : prénom, email, téléphone. C'est tout.\n\n**Erreur 6 : Pas de réponse rapide**\n\nSi vous mettez 48h à répondre, votre prospect a déjà contacté 3 concurrents.\n\n**Erreur 7 : Un site non sécurisé (HTTP)**\n\nGoogle affiche \"Non sécurisé\" sur les sites sans HTTPS. Aucun visiteur sérieux ne vous contactera.",
                'coverImage' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=1200&q=80',
                'readTime' => 5,
                'publishedAt' => new \DateTimeImmutable('-20 days'),
            ],
            [
                'title' => 'Abonnement web : la révolution silencieuse pour les PME',
                'category' => 'Actualité',
                'excerpt' => 'De plus en plus d\'entreprises choisissent l\'abonnement plutôt que l\'achat unique pour leur site web. Voici pourquoi c\'est (souvent) la meilleure décision.',
                'content' => "Le modèle par abonnement bouleverse le marché de la création de sites web. Fini les grosses factures de départ, place à un coût mensuel prévisible.\n\n**Le problème de l'achat unique**\n\nVous payez 3 000€ pour un site. Deux ans plus tard, il est obsolète, plus personne ne s'en occupe, et vous devez recommencer. Le vrai coût total sur 5 ans est souvent bien plus élevé que vous ne le pensez.\n\n**L'abonnement : zéro surprise**\n\nUn tarif mensuel fixe qui inclut : l'hébergement, les mises à jour, la sécurité, et le support. Votre site reste toujours à jour, toujours sécurisé.\n\n**La flexibilité avant tout**\n\nVous pouvez évoluer : changer de formule, ajouter des fonctionnalités, adapter votre site à la croissance de votre activité.\n\n**Pour qui c'est fait ?**\n\nL'abonnement web est idéal pour les TPE et PME qui veulent une présence professionnelle sans gérer la technique et sans sortir un gros budget d'un coup.\n\n**Les chiffres parlent**\n\nSelon nos données, les clients en abonnement ont un site 3x plus souvent mis à jour et 2x plus de leads générés que ceux avec un site acheté et \"oublié\".",
                'coverImage' => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=1200&q=80',
                'readTime' => 7,
                'publishedAt' => new \DateTimeImmutable('-30 days'),
            ],
            [
                'title' => 'Comment choisir son agence web : le guide honnête',
                'category' => 'Conseils',
                'excerpt' => 'Pas de langue de bois. Voici les vraies questions à poser avant de confier votre projet web, et les red flags à fuir absolument.',
                'content' => "Choisir une agence web est une décision importante. Voici comment ne pas se tromper.\n\n**Les bonnes questions à poser**\n\n• Qui va travailler sur mon projet ? (méfiez-vous si tout est sous-traité)\n• Pouvez-vous me montrer 3 sites récents similaires au mien ?\n• Qui sera mon interlocuteur au quotidien ?\n• Que se passe-t-il si je veux partir ? Est-ce que je garde mon site ?\n• Quels sont vos délais moyens de réponse au support ?\n\n**Les red flags à fuir**\n\n🚩 Prix anormalement bas (moins de 500€ pour un site sur mesure)\n🚩 Aucun exemple de réalisations sur leur propre site\n🚩 Contrat qui vous prive de la propriété de votre site\n🚩 Absence de mentions légales sur leur site\n🚩 Pression commerciale excessive pour signer vite\n\n**Ce qui compte vraiment**\n\nLa technique, c'est 40% du travail. Les 60% restants, c'est la communication, le respect des délais, et la compréhension de votre métier. Choisissez une agence avec qui vous avez envie de travailler sur la durée.\n\n**Notre approche**\n\nChez EntryWeb, on préfère vous expliquer honnêtement ce qu'on peut faire pour vous plutôt que de vous promettre la lune. Un appel découverte gratuit de 30 minutes pour voir si on est faits pour travailler ensemble.",
                'coverImage' => 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=1200&q=80',
                'readTime' => 6,
                'publishedAt' => new \DateTimeImmutable('-45 days'),
            ],
            [
                'title' => 'Les tendances web design 2025 qui vont dominer',
                'category' => 'Inspiration',
                'excerpt' => 'Minimalisme poussé, typographies expressives, micro-animations... On décrypte les tendances qui définissent le web design cette année.',
                'content' => "Le web design évolue vite. Voici ce qui marque 2025.\n\n**1. Le minimalisme brutal**\n\nFini les sites surchargés. Les marques qui inspirent confiance misent sur l'espace blanc, la lisibilité et une palette de couleurs réduite à 2-3 teintes maximum.\n\n**2. La typographie comme élément graphique**\n\nLes titres géants, les fontes expressives et les jeux typographiques remplacent les visuels complexes. Le texte devient l'image.\n\n**3. Les micro-animations utiles**\n\nPas des animations pour épater, mais des animations qui guident : hover states, transitions de page fluides, feedback visuel sur les interactions. L'utilisateur comprend instinctivement ce qui se passe.\n\n**4. Le glassmorphisme discret**\n\nEffets de verre dépoli, fonds floutés, transparences subtiles. Moderne sans être ostentatoire.\n\n**5. L'accessibilité au cœur du design**\n\nContraste élevé, taille de texte lisible, navigation au clavier. L'accessibilité n'est plus une option, c'est un standard.\n\n**6. Le dark mode natif**\n\nDe plus en plus de sites proposent un thème sombre automatique selon les préférences système. Ça réduit la fatigue oculaire et donne un aspect premium.\n\n**Notre recommandation**\n\nSuivez les tendances avec discernement. Ce qui compte, c'est que votre site soit cohérent avec votre identité de marque, pas juste \"à la mode\".",
                'coverImage' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&q=80',
                'readTime' => 5,
                'publishedAt' => new \DateTimeImmutable('-60 days'),
            ],
            [
                'title' => 'Google Analytics 4 : les métriques qui comptent vraiment',
                'category' => 'Tutoriel',
                'excerpt' => 'GA4 noie les débutants dans les données. Voici les 8 indicateurs à surveiller chaque semaine pour piloter votre présence web efficacement.',
                'content' => "Google Analytics 4 est puissant mais complexe. On simplifie.\n\n**Les 8 métriques essentielles**\n\n**1. Sessions** : combien de fois votre site a été visité. Une session = une visite, quelle que soit sa durée.\n\n**2. Utilisateurs actifs** : le nombre de personnes uniques qui ont visité votre site. Plus représentatif que les sessions.\n\n**3. Taux d'engagement** : le contraire du taux de rebond. Un visiteur \"engagé\" a passé plus de 10 secondes, visité 2 pages, ou déclenché un événement.\n\n**4. Pages vues par session** : plus c'est élevé, plus votre contenu retient l'attention.\n\n**5. Durée moyenne d'engagement** : combien de temps les gens restent actifs sur votre site.\n\n**6. Sources de trafic** : d'où viennent vos visiteurs ? SEO, réseaux sociaux, direct, publicité ?\n\n**7. Conversions** : les actions importantes (formulaire envoyé, téléphone cliqué, PDF téléchargé). À configurer selon vos objectifs.\n\n**8. Pages les plus vues** : votre contenu qui performe. Investissez davantage dessus.\n\n**Le tableau de bord hebdomadaire**\n\n15 minutes par semaine suffisent : regardez l'évolution des sessions, le taux d'engagement, vos sources de trafic et vos conversions. Comparez semaine après semaine.",
                'coverImage' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&q=80',
                'readTime' => 9,
                'publishedAt' => new \DateTimeImmutable('-75 days'),
            ],
        ];

        foreach ($articles as $data) {
            $article = new Article();
            $article->setTitle($data['title']);
            $article->setSlug($this->slugger->slug($data['title'])->lower()->toString());
            $article->setExcerpt($data['excerpt']);
            $article->setContent($data['content']);
            $article->setCategory($data['category']);
            $article->setCoverImage($data['coverImage']);
            $article->setReadTime($data['readTime']);
            $article->setIsPublished(true);
            $article->setPublishedAt($data['publishedAt']);
            $manager->persist($article);
        }

        $manager->flush();
    }
}
