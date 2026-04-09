<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

/**
 * @extends AbstractCrudController<Client>
 */
class ClientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets->addHtmlContentToBody(<<<'HTML'
<script>
document.addEventListener('DOMContentLoaded', function () {
    const amounts = { starter: 49, confort: 79, premium: 99 };
    const subscriptionField = document.querySelector('[name$="[subscription]"]');
    const amountField = document.querySelector('[name$="[monthlyAmount]"]');

    if (subscriptionField && amountField) {
        subscriptionField.addEventListener('change', function () {
            const val = this.value;
            if (amounts[val] !== undefined) {
                amountField.value = amounts[val];
            }
        });
    }
});
</script>
HTML);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Client')
            ->setEntityLabelInPlural('Base de données clients')
            ->setSearchFields(['firstName', 'lastName', 'email', 'company'])
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(25);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn (Action $a) => $a->setLabel('Voir fiche')->setIcon('fa fa-eye'));
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('pack', 'Pack')->setChoices([
                'Essentiel' => Client::PACK_ESSENTIEL,
                'Business' => Client::PACK_BUSINESS,
                'E-commerce' => Client::PACK_ECOMMERCE,
            ]))
            ->add(ChoiceFilter::new('subscription', 'Abonnement')->setChoices([
                'Starter' => Client::SUBSCRIPTION_STARTER,
                'Confort' => Client::SUBSCRIPTION_CONFORT,
                'Premium' => Client::SUBSCRIPTION_PREMIUM,
            ]))
            ->add(BooleanFilter::new('hasActiveIssue', 'Problème en cours'))
            ->add(BooleanFilter::new('setupFeePaid', 'Frais de mise en service payés'));
    }

    public function configureFields(string $pageName): iterable
    {
        $isIndex = Crud::PAGE_INDEX === $pageName;

        // Liste : uniquement nom + entreprise (le bouton "Voir fiche" est géré par configureActions)
        if ($isIndex) {
            yield TextField::new('fullName', 'Client');
            yield TextField::new('company', 'Entreprise');

            return;
        }

        // --- Informations client ---
        yield FormField::addPanel('Informations client');
        yield TextField::new('firstName', 'Prénom')->setColumns(4);
        yield TextField::new('lastName', 'Nom')->setColumns(4);
        yield EmailField::new('email', 'Email')->setColumns(4);
        yield TextField::new('phone', 'Téléphone')->setRequired(false)->setColumns(4);
        yield TextField::new('company', 'Entreprise')->setRequired(false)->setColumns(4);

        // --- Contrat ---
        yield FormField::addPanel('Contrat');
        yield ChoiceField::new('pack', 'Pack')
            ->setChoices([
                'Essentiel' => Client::PACK_ESSENTIEL,
                'Business' => Client::PACK_BUSINESS,
                'E-commerce' => Client::PACK_ECOMMERCE,
            ])
            ->renderAsBadges([
                Client::PACK_ESSENTIEL => 'info',
                Client::PACK_BUSINESS => 'primary',
                Client::PACK_ECOMMERCE => 'warning',
            ])
            ->setColumns(4);

        yield ChoiceField::new('subscription', 'Abonnement')
            ->setChoices([
                'Starter' => Client::SUBSCRIPTION_STARTER,
                'Confort' => Client::SUBSCRIPTION_CONFORT,
                'Premium' => Client::SUBSCRIPTION_PREMIUM,
            ])
            ->renderAsBadges([
                Client::SUBSCRIPTION_STARTER => 'secondary',
                Client::SUBSCRIPTION_CONFORT => 'info',
                Client::SUBSCRIPTION_PREMIUM => 'success',
            ])
            ->setColumns(4);

        yield DateField::new('contractStartDate', 'Début du contrat')->setColumns(4);
        yield IntegerField::new('totalMonths', 'Durée (mois)')->setColumns(3);

        // --- Paiements ---
        yield FormField::addPanel('Paiements');
        yield MoneyField::new('monthlyAmount', 'Mensualité')
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->setColumns(3);

        yield IntegerField::new('monthsPaid', 'Mois payés')
            ->onlyOnDetail()
            ->setColumns(3);

        yield IntegerField::new('monthsRemaining', 'Mois restants')
            ->onlyOnDetail()
            ->setColumns(3);

        yield MoneyField::new('totalPaid', 'Total encaissé')
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->onlyOnDetail()
            ->setColumns(3);

        yield BooleanField::new('setupFeePaid', 'Frais de mise en service payés')
            ->renderAsSwitch(true)
            ->setColumns(6);

        // --- Maintenance & Suivi ---
        yield FormField::addPanel('Maintenance & Suivi');
        yield NumberField::new('maintenanceHoursUsed', 'Heures maintenance utilisées (mois en cours)')
            ->setNumDecimals(1)
            ->setColumns(4);

        yield BooleanField::new('hasActiveIssue', 'Problème en cours')
            ->renderAsSwitch(true)
            ->setColumns(4);

        yield TextareaField::new('issueHistory', 'Historique des problèmes')
            ->setRequired(false)
            ->setNumOfRows(4)
            ->setColumns(12);

        // --- Notes ---
        yield FormField::addPanel('Notes');
        yield TextareaField::new('notes', 'Notes internes')
            ->setRequired(false)
            ->setNumOfRows(4)
            ->setColumns(12);

        yield DateField::new('createdAt', 'Client depuis')
            ->onlyOnDetail()
            ->setColumns(4);
    }
}
