<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_home', methods: 'GET')]
    public function home(): Response
    {
        // Vérifier si l'utilisateur a le rôle requis (par exemple ROLE_ADMIN) avant d'autoriser l'accès
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }
        
        return $this->render('backoffice/main/main.html.twig');
    }
}
