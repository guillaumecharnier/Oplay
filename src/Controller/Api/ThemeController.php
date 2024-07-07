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
    #[Route('/browse', name: 'browse', methods: "GET")]
    public function browse(ThemeRepository $themeRepository): JsonResponse
    {
        $allThemes = $themeRepository->findAll();
        
        return $this->json($allThemes, Response::HTTP_OK, [], ["groups" => "theme_browse"]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show($id, ThemeRepository $themeRepository): JsonResponse
    {
        $theme= $themeRepository->find($id);

        if (is_null($theme)) {
            $info = [
                'success' => false,
                'error_message' => 'Theme non trouvé',
                'error_code' => 'Theme_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        return $this->json($theme, Response::HTTP_OK, [], ['groups' => 'theme_show']);
    }

}

