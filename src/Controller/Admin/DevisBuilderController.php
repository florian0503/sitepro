<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Config\DevisGrid;
use App\Entity\Devis;
use App\Entity\DevisItem;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DevisBuilderController extends AbstractController
{
    #[Route('/devis/builder', name: 'admin_devis_builder')]
    public function build(
        Request $request,
        EntityManagerInterface $em,
        DevisRepository $devisRepository,
        AdminUrlGenerator $urlGenerator,
    ): Response {
        $categories = DevisGrid::getCategories();
        $offers = DevisGrid::getOffers();

        if ($request->isMethod('POST')) {
            $devis = new Devis();
            $devis->setReference($devisRepository->getNextReference());
            $devis->setClientFirstName((string) $request->request->get('client_first_name'));
            $devis->setClientLastName((string) $request->request->get('client_last_name'));
            $devis->setClientEmail((string) $request->request->get('client_email'));
            $devis->setClientPhone($request->request->get('client_phone'));
            $devis->setClientCompany($request->request->get('client_company'));
            $devis->setClientAddress($request->request->get('client_address'));
            $devis->setClientSiret($request->request->get('client_siret'));
            $devis->setNotes($request->request->get('notes'));

            $selectedOfferIndex = $request->request->get('selected_offer');
            if ($selectedOfferIndex !== null && isset($offers[(int) $selectedOfferIndex])) {
                $offer = $offers[(int) $selectedOfferIndex];
                $offerItem = new DevisItem();
                $offerItem->setCategoryName('Offre de base');
                $offerItem->setItemName($offer['name']);
                $offerItem->setDescription($offer['description']);
                $offerItem->setPrice($offer['price'] ?? 0.0);
                $offerItem->setIsMonthly(false);
                $devis->addItem($offerItem);
            }

            /** @var array<string> $selectedItems */
            $selectedItems = $request->request->all('items');

            foreach ($categories as $catIndex => $category) {
                foreach ($category['items'] as $itemIndex => $item) {
                    $key = $catIndex.'_'.$itemIndex;
                    if (\in_array($key, $selectedItems, true)) {
                        $devisItem = new DevisItem();
                        $devisItem->setCategoryName($category['name']);
                        $devisItem->setItemName($item['name']);
                        $devisItem->setDescription($item['description']);
                        $devisItem->setPrice($item['price']);
                        $devisItem->setIsMonthly($item['isMonthly']);
                        $devis->addItem($devisItem);
                    }
                }
            }

            $devis->computeTotals();
            $em->persist($devis);
            $em->flush();

            $url = $urlGenerator
                ->setController(DevisCrudController::class)
                ->setAction('detail')
                ->setEntityId($devis->getId())
                ->generateUrl();

            return $this->redirect($url);
        }

        return $this->render('admin/devis/builder.html.twig', [
            'categories' => $categories,
            'offers' => $offers,
        ]);
    }
}
