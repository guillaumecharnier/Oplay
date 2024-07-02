<?php

namespace App\Controller\Api;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'app_games_index', methods: ['GET'])]
    public function games(GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findAll();

        // Transform the game into an array
        $gameData = [];
        foreach ($games as $game) {
            $gameData[] = [
                'id' => $game->getId(),
                'name' => $game->getName(),
                'release_date' => $game->getReleaseDate(),
                'created_at' => $game->getCreatedAt(),
                'picture' => $game->getPicture(),
                'description' => $game->getDescription(),
                'editor' => $game->getEditor(),
            ];
        }

        // Return the data in JSON
        return $this->json([
            'games' => $gameData,
        ]);
    }

    #[Route('/game/{id}', name: 'app_game_index', methods: ['GET'])]
    public function game(int $id, GameRepository $gameRepository): Response
    {
        $game = $gameRepository->find($id);
        if (!$game) {
            throw $this->createNotFoundException('Game not found');
        }

        // Transform the game into an array
            $gameData[] = [
                'id' => $game->getId(),
                'name' => $game->getName(),
                'release_date' => $game->getReleaseDate(),
                'created_at' => $game->getCreatedAt(),
                'picture' => $game->getPicture(),
                'description' => $game->getDescription(),
                'editor' => $game->getEditor(),
            ];
                   // Return the data in JSON
        return $this->json([
            'game' => $gameData,
        ]);
        }
    }
    

