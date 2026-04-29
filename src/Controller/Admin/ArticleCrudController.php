<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Service\NewsletterMailerService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

/**
 * @extends AbstractCrudController<Article>
 */
class ArticleCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly NewsletterMailerService $newsletterMailer,
    ) {
    }

    public const CATEGORIES = [
        'Conseils' => 'Conseils',
        'Actualité' => 'Actualité',
        'Tutoriel' => 'Tutoriel',
        'Inspiration' => 'Inspiration',
        'Annonce' => 'Annonce',
    ];

    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article')
            ->setEntityLabelInPlural('Articles')
            ->setSearchFields(['title', 'excerpt', 'category'])
            ->setDefaultSort(['publishedAt' => 'DESC'])
            ->setPaginatorPageSize(20);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('isPublished', 'Publié'))
            ->add(ChoiceFilter::new('category', 'Catégorie')->setChoices(self::CATEGORIES));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('title', 'Titre')->setRequired(true)->setColumns(8);

        yield SlugField::new('slug', 'Slug')
            ->setTargetFieldName('title')
            ->setColumns(4)
            ->hideOnIndex();

        yield ChoiceField::new('category', 'Catégorie')
            ->setChoices(self::CATEGORIES)
            ->setRequired(true)
            ->setColumns(4);

        yield IntegerField::new('readTime', 'Lecture (min)')
            ->setColumns(2)
            ->hideOnIndex();

        yield BooleanField::new('isPublished', 'Publié')->setColumns(2);

        yield DateTimeField::new('publishedAt', 'Date de publication')
            ->setColumns(4)
            ->hideOnIndex();

        yield UrlField::new('coverImage', 'Image de couverture (URL)')
            ->setRequired(false)
            ->setColumns(12)
            ->hideOnIndex();

        yield TextareaField::new('excerpt', 'Extrait')
            ->setRequired(true)
            ->setColumns(12)
            ->hideOnIndex();

        yield TextareaField::new('content', 'Contenu')
            ->setRequired(true)
            ->setColumns(12)
            ->setNumOfRows(20)
            ->hideOnIndex();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $originalData = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
        $wasPublished = $originalData['isPublished'] ?? false;

        parent::updateEntity($entityManager, $entityInstance);

        if (!$wasPublished && $entityInstance->isPublished()) {
            $sent = $this->newsletterMailer->sendNewArticleNotification($entityInstance);
            if ($sent > 0) {
                $this->addFlash('success', "Newsletter envoyée à {$sent} abonné(s).");
            }
        }
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        if ($entityInstance->isPublished()) {
            $sent = $this->newsletterMailer->sendNewArticleNotification($entityInstance);
            if ($sent > 0) {
                $this->addFlash('success', "Newsletter envoyée à {$sent} abonné(s).");
            }
        }
    }
}
