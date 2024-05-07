<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AffiliateController extends AbstractController
{
    public function getAffiliateListPage(): Response
    {
        return $this->render('affiliate/affiliateListPage.html.twig', []);
    }

    public function getAffiliateCardPage(): Response
    {
        return $this->render('affiliate/affiliateCardPage.html.twig', []);
    }
}