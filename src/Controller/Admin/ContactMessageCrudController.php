<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ContactMessage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

/**
 * @extends AbstractCrudController<ContactMessage>
 */
class ContactMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContactMessage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Message de contact')
            ->setEntityLabelInPlural('Messages de contact')
            ->setSearchFields(['name', 'email', 'projectType', 'message'])
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(20);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('isRead', 'Lu'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('name', 'Nom')
            ->setFormTypeOption('disabled', true);

        yield EmailField::new('email', 'Email')
            ->setFormTypeOption('disabled', true);

        yield TextField::new('projectType', 'Type de projet')
            ->setFormTypeOption('disabled', true);

        yield TextField::new('budget', 'Budget')
            ->setFormTypeOption('disabled', true)
            ->hideOnIndex();

        yield TextareaField::new('message', 'Message')
            ->setFormTypeOption('disabled', true)
            ->hideOnIndex();

        yield BooleanField::new('isRead', 'Lu')
            ->renderAsSwitch(true);

        yield DateTimeField::new('createdAt', 'Reçu le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm');
    }
}
