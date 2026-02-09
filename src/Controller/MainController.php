<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Entity\Parrainage;
use App\Repository\CategoryRepository;
use App\Repository\ParrainageRepository;
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
    public function home(Request $request, RealisationRepository $realisationRepository): Response
    {
        $ref = $request->query->getString('ref');
        if ($ref !== '') {
            $request->getSession()->set('referral_code', $ref);
        }

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
    public function contact(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, ParrainageRepository $parrainageRepository): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->getString('name');
            $clientEmail = $request->request->getString('email');
            $budget = $request->request->getString('budget');
            $message = $request->request->getString('message');
            $referralCode = $request->request->getString('referral_code');

            // Find referral sponsor
            $parrainEmail = '';
            if ($referralCode !== '') {
                $parrain = $parrainageRepository->findByReferralCode($referralCode);
                if ($parrain !== null) {
                    $parrainEmail = $parrain->getEmail();
                    $parrain->setReferralsCount($parrain->getReferralsCount() + 1);
                }
            }

            $contactMessage = new ContactMessage();
            $contactMessage->setName($name);
            $contactMessage->setEmail($clientEmail);
            $contactMessage->setProjectType($budget);
            $contactMessage->setBudget($budget);
            $contactMessage->setMessage($message);
            if ($parrainEmail !== '') {
                $contactMessage->setReferredBy($parrainEmail);
            }

            $entityManager->persist($contactMessage);
            $entityManager->flush();

            $parrainRow = '';
            $parrainText = '';
            if ($parrainEmail !== '') {
                $parrainRow = <<<HTML
                        <tr>
                            <td style="padding: 12px 16px; background: #ecfdf5; border-bottom: 1px solid #f0f0f0; font-size: 13px; color: #065f46; vertical-align: top; font-weight: 600;">Parrainé par</td>
                            <td style="padding: 12px 16px; background: #ecfdf5; border-bottom: 1px solid #f0f0f0; font-size: 15px; color: #065f46;">{$parrainEmail}</td>
                        </tr>
                HTML;
                $parrainText = "Parrainé par : {$parrainEmail}\n";
            }

            $htmlContent = <<<HTML
            <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;">
                <div style="background: #0066ff; padding: 32px; text-align: center;">
                    <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">EntryWeb</h1>
                    <p style="color: #cce0ff; margin: 8px 0 0; font-size: 14px;">Nouveau message de contact</p>
                </div>
                <div style="padding: 32px; background: #f8f9fa; border: 1px solid #e9ecef;">
                    <table style="width: 100%; border-collapse: collapse;">
                        {$parrainRow}
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
                ->text("{$parrainText}Nom : {$name}\nEmail : {$clientEmail}\nFormule : {$budget}\n\nMessage :\n{$message}");

            $mailer->send($email);

            $request->getSession()->remove('referral_code');
            $this->addFlash('success', 'contact_sent');

            return $this->redirectToRoute('app_contact');
        }

        // Check if a referral code is in session
        $parrain = null;
        $referralCode = $request->getSession()->get('referral_code');
        if ($referralCode !== null) {
            $parrain = $parrainageRepository->findByReferralCode($referralCode);
        }

        return $this->render('pages/contact.html.twig', [
            'parrain' => $parrain,
        ]);
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

    #[Route('/parrainage', name: 'app_parrainage', methods: ['GET', 'POST'])]
    public function parrainage(Request $request, ParrainageRepository $parrainageRepository, EntityManagerInterface $entityManager): Response
    {
        $parrainage = null;

        // Store referral code in session when visiting with ?ref=
        $ref = $request->query->getString('ref');
        if ($ref !== '') {
            $request->getSession()->set('referral_code', $ref);
        }

        if ($request->isMethod('POST')) {
            $email = trim($request->request->getString('email'));

            if ($email !== '') {
                $parrainage = $parrainageRepository->findByEmail($email);

                if ($parrainage === null) {
                    do {
                        $code = 'ENT-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
                    } while ($parrainageRepository->findByReferralCode($code) !== null);

                    $parrainage = new Parrainage();
                    $parrainage->setEmail($email);
                    $parrainage->setReferralCode($code);

                    $entityManager->persist($parrainage);
                    $entityManager->flush();
                }

                $request->getSession()->set('parrainage_email', $email);

                return $this->redirect($this->generateUrl('app_parrainage').'#devenir-parrain');
            }
        }

        // Retrieve parrainage from session after redirect
        $parrainageEmail = $request->getSession()->remove('parrainage_email');
        if ($parrainageEmail !== null) {
            $parrainage = $parrainageRepository->findByEmail($parrainageEmail);
        }

        return $this->render('pages/parrainage.html.twig', [
            'parrainage' => $parrainage,
        ]);
    }
}
