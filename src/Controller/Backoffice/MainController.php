<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MainController extends AbstractController
{
    #[Route('/back/', name: 'app_back_main_home', methods: 'GET')]
    public function home(): Response
    {

        return $this->render('backoffice/main/main.html.twig');
    }
}
