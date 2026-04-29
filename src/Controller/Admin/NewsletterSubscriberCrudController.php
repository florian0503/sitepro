<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\NewsletterSubscriber;
use App\Service\LeadMagnetPdfService;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

#[AdminCrud(routePath: '/newsletter', routeName: 'admin_newsletter')]
class NewsletterSubscriberCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly LeadMagnetPdfService $pdfService,
        #[Autowire('%env(CONTACT_EMAIL)%')]
        private readonly string $contactEmail,
    ) {
    }

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
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des abonnés newsletter')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un abonné');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::EDIT);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield EmailField::new('email', 'Email');
        yield DateTimeField::new('createdAt', 'Inscrit le')
            ->setFormat('dd/MM/yyyy HH:mm')
            ->hideOnForm();
        yield BooleanField::new('pdfSent', 'PDF envoyé')
            ->renderAsSwitch(false)
            ->hideOnForm();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('pdfSent');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        $email = $entityInstance->getEmail();
        $pdfContent = $this->pdfService->generate();

        $htmlPdf = <<<HTML
        <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background: #0066ff; padding: 32px; text-align: center;">
                <h1 style="color: #fff; margin: 0; font-size: 24px;">EntryWeb</h1>
                <p style="color: #cce0ff; margin: 8px 0 0; font-size: 14px;">Votre guide gratuit est arrivé !</p>
            </div>
            <div style="padding: 40px 32px;">
                <h2 style="font-size: 20px; color: #1a1a2e; margin: 0 0 16px;">Voici votre checklist</h2>
                <p style="font-size: 15px; color: #555; line-height: 1.7; margin: 0 0 32px;">
                    Vous trouverez en pièce jointe le guide <strong>"Les 10 erreurs qui font fuir vos clients en ligne"</strong>.
                </p>
                <div style="text-align: center;">
                    <a href="https://entryweb.fr/contact" style="padding: 14px 32px; background: #0066ff; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600;">Obtenir mon devis gratuit</a>
                </div>
            </div>
        </div>
        HTML;

        try {
            $this->mailer->send(
                (new Email())
                    ->from($this->contactEmail)
                    ->to($email)
                    ->subject('Votre guide gratuit EntryWeb — Les 10 erreurs qui font fuir vos clients')
                    ->html($htmlPdf)
                    ->text('Votre guide est en pièce jointe.')
                    ->addPart(new DataPart($pdfContent, 'guide-entryweb-10-erreurs.pdf', 'application/pdf'))
            );
            $entityInstance->setPdfSent(true);
            $entityManager->flush();
            $this->addFlash('success', "PDF envoyé à {$email}.");
        } catch (TransportExceptionInterface) {
            $this->addFlash('warning', "Abonné ajouté mais l'envoi du PDF a échoué.");
        }
    }
}
