<?php

namespace App\Controller\Api;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/game', name: 'app_api_game_')]
class GameController extends AbstractController
{
    #[Route('/browse', name: 'browse', methods: "GET")]
    public function browse(GameRepository $tagRepository): JsonResponse
    {
        $allTags = $tagRepository->findAll();
        
        return $this->json($allTags, Response::HTTP_OK, [], ["groups" => "game_browse"]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show($id, GameRepository $gameRepository): JsonResponse
    {
        $game= $gameRepository->find($id);

        if (is_null($game)) {
            $info = [
                'success' => false,
                'error_message' => 'Tag non trouvé',
                'error_code' => 'Tag_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($game, Response::HTTP_OK, [], ['groups' => 'game_show']);
    }

    

}
    

