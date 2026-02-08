<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Repository\CategoryRepository;
use App\Repository\RealisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(RealisationRepository $realisationRepository): Response
    {
        $realisations = $realisationRepository->findPublished();

        return $this->render('pages/home.html.twig', [
            'realisations' => array_slice($realisations, 0, 4),
        ]);
    }

    #[Route('/offres', name: 'app_offers')]
    public function offers(): Response
    {
        return $this->render('pages/offers.html.twig');
    }

    #[Route('/realisations', name: 'app_portfolio')]
    public function portfolio(RealisationRepository $realisationRepository, CategoryRepository $categoryRepository): Response
    {
        $realisations = $realisationRepository->findPublished();
        $categories = $categoryRepository->findAllOrderedByName();

        return $this->render('pages/portfolio.html.twig', [
            'realisations' => $realisations,
            'categories' => $categories,
        ]);
    }

    #[Route('/a-propos', name: 'app_about')]
    public function about(RealisationRepository $realisationRepository): Response
    {
        $realisationsCount = count($realisationRepository->findPublished());

        return $this->render('pages/about.html.twig', [
            'realisationsCount' => $realisationsCount,
        ]);
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->getString('name');
            $clientEmail = $request->request->getString('email');
            $budget = $request->request->getString('budget');
            $message = $request->request->getString('message');

            $contactMessage = new ContactMessage();
            $contactMessage->setName($name);
            $contactMessage->setEmail($clientEmail);
            $contactMessage->setProjectType($request->request->getString('project_type'));
            $contactMessage->setBudget($budget);
            $contactMessage->setMessage($message);

            $entityManager->persist($contactMessage);
            $entityManager->flush();

            $htmlContent = <<<HTML
            <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;">
                <div style="background: #0066ff; padding: 32px; text-align: center;">
                    <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">EntryWeb</h1>
                    <p style="color: #cce0ff; margin: 8px 0 0; font-size: 14px;">Nouveau message de contact</p>
                </div>
                <div style="padding: 32px; background: #f8f9fa; border: 1px solid #e9ecef;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 12px 16px; background: #ffffff; border-bottom: 1px solid #f0f0f0; font-size: 13px; color: #666; width: 120px; vertical-align: top;">Nom</td>
                            <td style="padding: 12px 16px; background: #ffffff; border-bottom: 1px solid #f0f0f0; font-size: 15px; color: #111;">{$name}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 16px; background: #ffffff; border-bottom: 1px solid #f0f0f0; font-size: 13px; color: #666; vertical-align: top;">Email</td>
                            <td style="padding: 12px 16px; background: #ffffff; border-bottom: 1px solid #f0f0f0; font-size: 15px;"><a href="mailto:{$clientEmail}" style="color: #111; text-decoration: none;">{$clientEmail}</a></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 16px; background: #ffffff; border-bottom: 1px solid #f0f0f0; font-size: 13px; color: #666; vertical-align: top;">Formule</td>
                            <td style="padding: 12px 16px; background: #ffffff; border-bottom: 1px solid #f0f0f0; font-size: 15px; color: #111;">{$budget}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 16px; background: #ffffff; font-size: 13px; color: #666; vertical-align: top;">Message</td>
                            <td style="padding: 12px 16px; background: #ffffff; font-size: 15px; color: #111; line-height: 1.6;">{$message}</td>
                        </tr>
                    </table>
                </div>
                <div style="padding: 24px; text-align: center; background: #0066ff;">
                    <a href="mailto:{$clientEmail}" style="display: inline-block; padding: 12px 32px; background: #ffffff; color: #0066ff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px;">Repondre a {$name}</a>
                    <p style="color: #cce0ff; margin: 16px 0 0; font-size: 12px;">EntryWeb - L'agence web des entrepreneurs</p>
                </div>
            </div>
            HTML;

            $email = (new Email())
                ->from('contact@entryweb.fr')
                ->to('contact@entryweb.fr')
                ->replyTo($clientEmail)
                ->subject('Nouveau contact - '.$name)
                ->html($htmlContent)
                ->text("Nom : {$name}\nEmail : {$clientEmail}\nFormule : {$budget}\n\nMessage :\n{$message}");

            $mailer->send($email);

            $this->addFlash('success', 'contact_sent');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('pages/contact.html.twig');
    }

    #[Route('/faq', name: 'app_faq')]
    public function faq(): Response
    {
        return $this->render('pages/faq.html.twig');
    }

    #[Route('/mentions-legales', name: 'app_legal')]
    public function legal(): Response
    {
        return $this->render('pages/legal.html.twig');
    }

    #[Route('/cgv', name: 'app_cgv')]
    public function cgv(): Response
    {
        return $this->render('pages/cgv.html.twig');
    }

    #[Route('/politique-confidentialite', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('pages/privacy.html.twig');
    }
}
