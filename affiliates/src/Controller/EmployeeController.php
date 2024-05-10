<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Common\GenderEnum;
use App\Model\Data\EmployeeDTO;
use App\Repository\AffiliateRepository;
use App\Repository\EmployeeRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EmployeeController extends AbstractController
{
    private EmployeeRepository $employeeRepository;
    private AffiliateRepository $affiliateRepository;

    public function __construct ()
    {
        $this->employeeRepository = new EmployeeRepository();
        $this->affiliateRepository = new AffiliateRepository();
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
//        catch (\Exception $e) {
//            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
    }

    public function getAddEmployeePage(int $affiliateId): Response
    {
//        try
//        {
            return $this->render('employee/employeeCardPage.html.twig', [
                'affiliate' => $this->affiliateRepository->findById($affiliateId),
                'genderNotStated' => GenderEnum::NOT_STATED->value,
                'genderMale' => GenderEnum::MALE->value,
                'genderFemale' => GenderEnum::FEMALE->value,
            ]);
//        }
//        catch (\Exception $e)
//        {
//            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
    }

    public function addEmployee(Request $request): Response
    {
//        try
//        {
            $employeeData = [
                'affiliate_id' => $request->request->get('affiliateId'),
                'first_name' => $request->request->get('firstName'),
                'last_name' => $request->request->get('lastName'),
                'middle_name' => $request->request->get('middleName'),
                'phone_number' => $request->request->get('phone'),
                'gender' => $request->request->get('gender'),
                'email' => $request->request->get('email'),
                'job_title' => $request->request->get('jobTitle'),
                'birth_date' => $request->request->get('birthdate'),
                'hire_date' => $request->request->get('hireDate'),
                'administrator_comment' => $request->request->get('comment'),
            ];

            $newEmployeeId = $this->employeeRepository->store($employeeData);

            return $this->redirectToRoute('employee_card_page', ['id' => $newEmployeeId]);
//        }
//        catch (\Exception $e)
//        {
//            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
    }

    public function updateEmployee(Request $request): Response
    {
//        try
//        {
            $id = (int)$request->request->get('id');
            $employeeData = new EmployeeDTO(
                id: $id,
                affiliateId: (int)$request->request->get('affiliateId'),
                firstName: $request->request->get('firstName'),
                lastName: $request->request->get('lastName'),
                middleName: $request->request->get('middleName'),
                phone: $request->request->get('phone'),
                email: $request->request->get('email'),
                jobTitle: $request->request->get('jobTitle'),
                gender: GenderEnum::from((int)$request->request->get('gender')),
                birthDate: new DateTimeImmutable($request->request->get('birthDate')),
                hireDate: new DateTimeImmutable($request->request->get('hireDate')),
                administratorComment: $request->request->get('comment'),
            );

            $this->employeeRepository->update($employeeData);

            return $this->redirectToRoute('employee_card_page', ['id' => $id]);
//        }
//        catch (\Exception $e)
//        {
//            return new Response('An error occurred: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
    }
}