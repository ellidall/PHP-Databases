<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Model\Affiliate;
use App\Model\Data\AffiliateDTO;
use App\Repository\AffiliateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AffiliateController extends AbstractController
{
    private AffiliateRepository $affiliateRepository;

    public function __construct ()
    {
        $this->affiliateRepository = new AffiliateRepository();
    }

    public function getAffiliateListPage(): Response
    {
        try
        {
            $affiliates = $this->getAffiliateList();
            return $this->render('affiliate/affiliateListPage.html.twig', [
                'affiliates' => $affiliates,
            ]);
        }
        catch (\Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAffiliateCardPage(int $id): Response
    {
        try {
            $affiliate = $this->affiliateRepository->findById($id);

            if (!$affiliate) {
                throw new NotFoundHttpException('Affiliate with ID = ' . $id . ' not found');
            }

            return $this->render('affiliate/affiliateCardPage.html.twig', [
                'affiliate' => $affiliate,
            ]);
        } catch (\Exception $e) {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function getAddAffiliatePage(): Response
    {
        return $this->render('affiliate/affiliateCardPage.html.twig', []);
    }

    public function addAffiliate(Request $request): Response
    {
        $affiliateData = [
            'city' => $request->request->get('city'),
            'address' => $request->request->get('address'),
            /*TODO: $employeeRepository->getCount();*/
            'employee_count' => (int) ($request->request->get('employeeCount') ?: 0),
        ];

        $newAffiliateId = $this->affiliateRepository->store($affiliateData);

        return $this->redirectToRoute('affiliate_card_page', ['id' => $newAffiliateId]);
    }

    public function updateAffiliate(Request $request): Response
    {
        $id = (int) $request->request->get('id');
        $affiliateData = new AffiliateDTO(
            $id,
            $request->request->get('city'),
            $request->request->get('address'),
            /*TODO: $employeeRepository->getCount();*/
            (int) ($request->request->get('employeeCount') ?: 0),
        );

        $this->affiliateRepository->update($affiliateData);

        return $this->redirectToRoute('affiliate_card_page', ['id' => $id]);
    }

    /**
     * @return Affiliate[]
     */
    private function getAffiliateList(): array
    {
        return $this->affiliateRepository->listAll();
    }
}