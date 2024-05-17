<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Common\ConnectionProvider;
use App\Database\AffiliateTable;
use App\Database\EmployeeTable;
use App\Model\Affiliate;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AffiliateController extends AbstractController
{
    private AffiliateTable $affiliateRepository;
    private EmployeeTable $employeeRepository;

    public function __construct ()
    {
        $connectionProvider = new ConnectionProvider();
        $connection = $connectionProvider->getConnection();
        $this->employeeRepository = new EmployeeTable($connection);
        $this->affiliateRepository = new AffiliateTable($connection);
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
        catch (Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAffiliateCardPage(int $id): Response
    {
//        try
//        {
            $affiliate = $this->affiliateRepository->findById($id);

            if (!$affiliate) {
                throw new NotFoundHttpException('Affiliate with ID = ' . $id . ' not found');
            }
            $emp = $this->employeeRepository->findByAffiliateId($id);
            return $this->render('affiliate/affiliateCardPage.html.twig', [
                'affiliate' => $affiliate,
                'employees' => $this->employeeRepository->findByAffiliateId($id),
            ]);
//        }
//        catch (NotFoundHttpException $e)
//        {
//            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_NOT_FOUND);
//        }
//        catch (Exception $e)
//        {
//            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
    }

    public function getAddAffiliatePage(): Response
    {
        try
        {
            return $this->render('affiliate/affiliateCardPage.html.twig', []);
        }
        catch (Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addAffiliate(Request $request): Response
    {
        try
        {
            $affiliate = new Affiliate(
                id: null,
                city: $request->request->get('city'),
                address: $request->request->get('address'),
                employeeCount: (int)($request->request->get('employeeCount') ?: 0),
            );

            $newAffiliateId = $this->affiliateRepository->create($affiliate);
            $affiliate->setId($newAffiliateId);

            return $this->redirectToRoute('affiliate_card_page', ['id' => $newAffiliateId]);
        }
        catch (Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateAffiliate(Request $request): Response
    {
        try
        {
            $id = (int)$request->request->get('id');
            $affiliate = new Affiliate(
                id: $id,
                city: $request->request->get('city'),
                address: $request->request->get('address'),
                employeeCount: count($this->employeeRepository->findByAffiliateId($id)),
            );

            $this->affiliateRepository->update($affiliate);

            return $this->redirectToRoute('affiliate_card_page', ['id' => $id]);
        }
        catch (Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteAffiliate(Request $request): Response
    {
        try
        {
            $id = (int)$request->request->get('id');
            $affiliate = $this->affiliateRepository->findById($id);
            $employees = $this->employeeRepository->findByAffiliateId($id);
            foreach ($employees as $employee) {
                $this->employeeRepository->delete($employee);
            }
            $this->affiliateRepository->delete($affiliate);

            $affiliates = $this->affiliateRepository->listAll();
            return $this->render('affiliate/affiliateListPage.html.twig', [
                'affiliates' => $affiliates,
            ]);
        }
        catch (Exception $e)
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