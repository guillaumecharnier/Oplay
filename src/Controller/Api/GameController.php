<?php

namespace App\Controller\Api;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api/game', name: 'app_api_game')]
class GameController extends AbstractController
{
    #[Route('/browse', name: 'browse', methods: ['GET'])]
    public function games(GameRepository $gameRepository): JsonResponse
    {
        $allGames = $gameRepository->findAll();
        // Return the data in JSON
        return $this->json($allGames, 200, [], ["groups" => "game_browse"]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function game(int $id, GameRepository $gameRepository): JsonResponse
    {
        $game = $gameRepository->find($id);
                   // Return the data in JSON
                    if (is_null($game)) {
                    $info = [
                        'success' => false,
                        'error_message' => 'Jeu non trouvée',
                        'error_code' => 'Jeu_not_found',
                    ];
                    return $this->json($info, Response::HTTP_NOT_FOUND);
                }
        
                // Traiter le cas où la catégorie est trouvée
                // Par exemple, retourner les données de la catégorie
                return $this->json($game, Response::HTTP_OK, [], ['groups' => 'game_show']);
        }
    }
    

