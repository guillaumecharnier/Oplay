<?php

namespace App\Controller\Api;

use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/theme', name: 'app_api_theme_')]
class ThemeController extends AbstractController
{
    #[Route('/', name: 'browse', methods: "GET")]
    public function browse(ThemeRepository $themeRepository): JsonResponse
    {
        // Récupérer tous les thèmes depuis le repository
        $allThemes = $themeRepository->findAll();
        
        // Retourner tous les thèmes sous forme de JSON avec le groupe 'theme_browse'
        return $this->json($allThemes, Response::HTTP_OK, [], ["groups" => "theme_browse"]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show($id, ThemeRepository $themeRepository): JsonResponse
    {
        // Trouver le thème spécifié par son ID
        $theme = $themeRepository->find($id);

        // Si le thème n'est pas trouvé, retourner une réponse JSON avec un message d'erreur
        if (is_null($theme)) {
            $info = [
                'success' => false,
                'error_message' => 'Theme non trouvé',
                'error_code' => 'Theme_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        // Retourner les détails du thème sous forme de JSON avec le groupe 'theme_show'
        return $this->json($theme, Response::HTTP_OK, [], ['groups' => 'theme_show']);
    }
}

