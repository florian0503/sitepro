<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Devis;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<Devis>
 */
class DevisCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Devis::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Devis')
            ->setEntityLabelInPlural('Devis')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(25)
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        $exportPdf = Action::new('exportPdf', 'Exporter PDF', 'fa fa-file-pdf')
            ->linkToRoute('admin_devis_pdf', fn (Devis $devis): array => ['id' => $devis->getId()])
            ->setHtmlAttributes(['target' => '_blank'])
            ->setCssClass('btn btn-primary');

        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn (Action $a) => $a->setLabel('Voir')->setIcon('fa fa-eye'))
            ->add(Crud::PAGE_DETAIL, $exportPdf)
            ->add(Crud::PAGE_INDEX, $exportPdf);
    }

    public function configureFields(string $pageName): iterable
    {
        $isIndex = Crud::PAGE_INDEX === $pageName;

        if ($isIndex) {
            yield TextField::new('reference', 'Référence');
            yield TextField::new('clientFullName', 'Client');
            yield TextField::new('clientCompany', 'Entreprise');
            yield NumberField::new('totalHt', 'Total (€)')
                ->setNumDecimals(2);
            yield DateTimeField::new('createdAt', 'Date');

            return;
        }

        yield TextField::new('reference', 'Référence');
        yield TextField::new('clientFirstName', 'Prénom');
        yield TextField::new('clientLastName', 'Nom');
        yield TextField::new('clientEmail', 'Email');
        yield TextField::new('clientPhone', 'Téléphone');
        yield TextField::new('clientCompany', 'Entreprise');
        yield TextField::new('clientAddress', 'Adresse');
        yield TextField::new('clientSiret', 'SIRET');

        yield MoneyField::new('totalHt', 'Total')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);
        yield NumberField::new('monthlyTotal', 'Abonnement mensuel (€/mois)')
            ->setNumDecimals(2);

        yield IntegerField::new('validityDays', 'Validité (jours)');
        yield TextareaField::new('notes', 'Notes');
        yield DateTimeField::new('createdAt', 'Créé le');
    }
}
