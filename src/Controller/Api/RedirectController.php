<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RedirectController extends AbstractController
{
    #[Route('/api/redirect', name: 'app_redirect', methods: 'GET')]
    
    public function redirectToBackend(): Response
    {
        // Vérification si l'utilisateur est authentifié avec un rôle spécifique
        if ($this->isGranted('ROLE_ADMIN')) {
            // Redirection vers le back-end distant
            return $this->redirect('http://localhost:8080');
        } else {
            throw $this->createAccessDeniedException('Access denied');
        }
    }
}