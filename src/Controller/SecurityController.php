<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): JsonResponse
    {
        // Obtenir l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier nom d'utilisateur saisi par l'utilisateur
        //$lastUsername = $authenticationUtils->getLastUsername();

        // Décoder les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données JSON sont valides
        if (empty($data['username']) || empty($data['password'])) {
            return new JsonResponse(['error' => 'Invalid username or password'], Response::HTTP_BAD_REQUEST);
        }

        // Déterminer le code de statut basé sur la présence d'une erreur
        $statusCode = $error ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        // Créer un tableau de données à renvoyer en JSON
        $responseData = [
            //'last_username' => $lastUsername,
            'error' => $error ? $error->getMessage() : null,
        ];

        // Renvoyer une réponse JSON avec le statut HTTP approprié
        return new JsonResponse($responseData, $statusCode);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
