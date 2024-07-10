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
    #[Route('/', name: 'browse', methods: "GET")]
    public function browse(GameRepository $gameRepository): JsonResponse
    {
        // Récupérer tous les jeux depuis le repository
        $allGames = $gameRepository->findAll();
        
        // Retourner tous les jeux sous forme de JSON avec le groupe 'game_browse'
        return $this->json($allGames, Response::HTTP_OK, [], ["groups" => "game_browse"]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show($id, GameRepository $gameRepository): JsonResponse
    {
        // Trouver le jeu spécifié par son ID
        $game = $gameRepository->find($id);

        // Si le jeu n'est pas trouvé, retourner une réponse JSON avec un message d'erreur
        if (is_null($game)) {
            $info = [
                'success' => false,
                'error_message' => 'Jeu non trouvé',
                'error_code' => 'Game_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }
        
        // Retourner les détails du jeu sous forme de JSON avec le groupe 'game_show'
        return $this->json($game, Response::HTTP_OK, [], ['groups' => 'game_show']);
    }
}
