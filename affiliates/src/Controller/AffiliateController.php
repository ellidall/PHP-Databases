<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Common\Database\ConnectionProvider;
use App\Database\AffiliateTable;
use App\Database\EmployeeTable;
use App\Model\Affiliate;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AffiliateController extends AbstractController
{
    private AffiliateTable $affiliateTable;
    private EmployeeTable $employeeTable;

    public function __construct ()
    {
        $connectionProvider = new ConnectionProvider();
        $connection = $connectionProvider->getConnection();
        $this->employeeTable = new EmployeeTable($connection);
        $this->affiliateTable = new AffiliateTable($connection);
    }

    public function getAffiliateListPage(): Response
    {
        try
        {
            $affiliates = $this->affiliateTable->listAll();
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
        try
        {
            $affiliate = $this->affiliateTable->findById($id);

            if (!$affiliate) {
                throw new NotFoundHttpException('Affiliate with ID = ' . $id . ' not found');
            }
            return $this->render('affiliate/affiliateCardPage.html.twig', [
                'affiliate' => $affiliate,
                'employees' => $this->employeeTable->findByAffiliateId($id),
            ]);
        }
        catch (NotFoundHttpException $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_NOT_FOUND);
        }
        catch (Exception $e)
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
                employeeCount: 0,
            );

            $newAffiliateId = $this->affiliateTable->insert($affiliate);
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
            $city = $request->request->get('city');
            $address = $request->request->get('address');
            $employeeCount = count($this->employeeTable->findByAffiliateId($id) ?? 0);

            $affiliate = $this->affiliateTable->findById($id);

            $affiliate->setCity($city);
            $affiliate->setAddress($address);
            $affiliate->setEmployeeCount($employeeCount);

            $this->affiliateTable->update($affiliate);

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
            $affiliate = $this->affiliateTable->findById($id);
            $employees = $this->employeeTable->findByAffiliateId($id);
            foreach ($employees as $employee) {
                $this->employeeTable->delete($employee);
            }
            $this->affiliateTable->delete($affiliate);

            $affiliates = $this->affiliateTable->listAll();
            return $this->render('affiliate/affiliateListPage.html.twig', [
                'affiliates' => $affiliates,
            ]);
        }
        catch (Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}