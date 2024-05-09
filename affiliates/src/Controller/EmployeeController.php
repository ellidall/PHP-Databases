<?php
declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends AbstractController
{
    public function getEmployeeCardPage(int $id): Response
    {
        print('employeeId = ' . $id);
        return $this->render('employee/employeeCardPage.html.twig', []);
    }
}