<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Model\Affiliate;
use App\Model\Data\AffiliateDTO;
use App\Repository\AffiliateRepository;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AffiliateController extends AbstractController
{
    private AffiliateRepository $affiliateRepository;
    private EmployeeRepository $employeeRepository;

    public function __construct ()
    {
        // getConnection
        $this->affiliateRepository = new AffiliateRepository();
//        $this->affiliateRepository = new AffiliateRepository(connection);
        $this->employeeRepository = new EmployeeRepository();
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
        try
        {
            $affiliate = $this->affiliateRepository->findById($id);

            if (!$affiliate) {
                throw new NotFoundHttpException('Affiliate with ID = ' . $id . ' not found');
            }

            return $this->render('affiliate/affiliateCardPage.html.twig', [
                'affiliate' => $affiliate,
                'employees' => $this->employeeRepository->findByAffiliateId($id),
            ]);
        }
        catch (NotFoundHttpException $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_NOT_FOUND);
        }
        catch (\Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAddAffiliatePage(): Response
    {
        try
        {
            return $this->render('affiliate/affiliateCardPage.html.twig', []);
        }
        catch (\Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addAffiliate(Request $request): Response
    {
        try
        {
            $affiliateData = [
                'city' => $request->request->get('city'),
                'address' => $request->request->get('address'),
                'employee_count' => (int) ($request->request->get('employeeCount') ?: 0),
            ];

            $newAffiliateId = $this->affiliateRepository->store($affiliateData);

            return $this->redirectToRoute('affiliate_card_page', ['id' => $newAffiliateId]);
        }
        catch (\Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Переделать на использование одного ДТО без id
    public function updateAffiliate(Request $request): Response
    {
        try
        {
            $id = (int) $request->request->get('id');
            $affiliateData = new AffiliateDTO(
                $id,
                $request->request->get('city'),
                $request->request->get('address'),
                count($this->employeeRepository->findByAffiliateId($id)),
            );

            $this->affiliateRepository->update($affiliateData);

            return $this->redirectToRoute('affiliate_card_page', ['id' => $id]);
        }
        catch (\Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return Affiliate[]
     */
    private function getAffiliateList(): array
    {
        return $this->affiliateRepository->listAll();
    }
}