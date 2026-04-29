<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\NewsletterSubscriber;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

#[AdminCrud(routePath: '/newsletter', routeName: 'admin_newsletter')]
class NewsletterSubscriberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return NewsletterSubscriber::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Abonné newsletter')
            ->setEntityLabelInPlural('Abonnés newsletter')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des abonnés newsletter');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield EmailField::new('email', 'Email');
        yield DateTimeField::new('createdAt', 'Inscrit le')->setFormat('dd/MM/yyyy HH:mm');
        yield BooleanField::new('pdfSent', 'PDF envoyé')->renderAsSwitch(false);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('pdfSent');
    }
}
