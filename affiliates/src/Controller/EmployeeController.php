<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Common\ConnectionProvider;
use App\Common\GenderEnum;
use App\Database\AffiliateTable;
use App\Database\EmployeeTable;
use App\Model\Employee;
use App\Service\ImageService;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EmployeeController extends AbstractController
{
    private EmployeeTable $employeeRepository;
    private AffiliateTable $affiliateRepository;
    private ImageService $imageService;

    public function __construct ()
    {
        $connectionProvider = new ConnectionProvider();
        $connection = $connectionProvider->getConnection();
        $this->employeeRepository = new EmployeeTable($connection);
        $this->affiliateRepository = new AffiliateTable($connection);
        $this->imageService = new ImageService();
    }

    public function getEmployeeCardPage(int $id): Response
    {
        try
        {
            $employee = $this->employeeRepository->findById($id);

            if (!$employee) {
                throw new NotFoundHttpException('Employee with ID = ' . $id . ' not found');
            }

            return $this->render('employee/employeeCardPage.html.twig', [
                'employee' => $employee,
                'affiliate' => $this->affiliateRepository->findById($employee->getAffiliateId()),
                'genderNotStated' => GenderEnum::NOT_STATED->value,
                'genderMale' => GenderEnum::MALE->value,
                'genderFemale' => GenderEnum::FEMALE->value,
            ]);
        }
        catch (NotFoundHttpException $e) {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_NOT_FOUND);
        }
        catch (Exception $e) {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAddEmployeePage(int $affiliateId): Response
    {
        try
        {
            return $this->render('employee/employeeCardPage.html.twig', [
                'affiliate' => $this->affiliateRepository->findById($affiliateId),
                'genderNotStated' => GenderEnum::NOT_STATED->value,
                'genderMale' => GenderEnum::MALE->value,
                'genderFemale' => GenderEnum::FEMALE->value,
            ]);
        }
        catch (Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function addEmployee(Request $request): Response
    {
        try
        {
            $avatar = $request->files->get('avatar');
            $imagePath = null;
            if ($avatar !== null)
            {
                $imagePath = $this->imageService->moveImageToUploadsAndGetPath($avatar);
            }

            $employee = new Employee(
                id: null,
                affiliateId: (int)$request->request->get('affiliateId'),
                firstName: $request->request->get('firstName'),
                lastName: $request->request->get('lastName'),
                middleName: $request->request->get('middleName'),
                phone: $request->request->get('phone'),
                email: $request->request->get('email'),
                jobTitle: $request->request->get('jobTitle'),
                gender: GenderEnum::from((int)$request->request->get('gender')),
                birthDate: new DateTimeImmutable($request->request->get('birthDate') ?: ''),
                hireDate: new DateTimeImmutable($request->request->get('hireDate') ?: ''),
                administratorComment: $request->request->get('comment'),
                avatar: $imagePath,
            );

            $newEmployeeId = $this->employeeRepository->create($employee);
            $employee->setId($newEmployeeId);

            return $this->redirectToRoute('employee_card_page', ['id' => $newEmployeeId]);
        }
        catch (Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function updateEmployee(Request $request): Response
    {
        try
        {
            $id = (int)$request->request->get('id');
            $avatar = $request->files->get('avatar');
            $imagePath = null;
            if ($avatar !== null)
            {
                $imagePath = $this->imageService->moveImageToUploadsAndGetPath($avatar);
            }

            $employee = new Employee(
                id: $id,
                affiliateId: (int)$request->request->get('affiliateId'),
                firstName: $request->request->get('firstName'),
                lastName: $request->request->get('lastName'),
                middleName: $request->request->get('middleName'),
                phone: $request->request->get('phone'),
                email: $request->request->get('email'),
                jobTitle: $request->request->get('jobTitle'),
                gender: GenderEnum::from((int)$request->request->get('gender')),
                birthDate: new DateTimeImmutable($request->request->get('birthDate') ?: ''),
                hireDate: new DateTimeImmutable($request->request->get('hireDate') ?: ''),
                administratorComment: $request->request->get('comment'),
                avatar: $imagePath,
            );

            $this->employeeRepository->update($employee);

            return $this->redirectToRoute('employee_card_page', ['id' => $id]);
        }
        catch (Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteEmployee(Request $request): Response
    {
        try
        {
            $id = (int)$request->request->get('id');
            $employee = $this->employeeRepository->findById((int)$request->request->get('id'));
            $this->employeeRepository->delete($employee);

            return $this->redirectToRoute('affiliate_card_page', [
                'id' => (int)$request->request->get('affiliateId'),
            ]);
        }
        catch (Exception $e)
        {
            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}