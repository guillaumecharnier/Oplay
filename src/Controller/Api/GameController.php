<?php

namespace App\Controller\Api;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/game', name: 'app_api_game_')]
class GameController extends AbstractController
{
    #[Route('/browse', name: 'browse', methods: ['GET'])]
    public function browse(GameRepository $gameRepository, SerializerInterface $serializer): JsonResponse
    {
        $games = $gameRepository->findAll();

        // Sérialiser les games
        $serializedGames = [];
        foreach ($games as $game) {
            $serializedGame = [
                'id' => $game->getId(),
                'name' => $game->getName(),
                'releaseDate' => $game->getReleaseDate(),
                'createdAt' => $game->getCreatedAt(),
                'picture' => $game->getPicture(),
                'price' => $game->getPrice(),
                'description' => $game->getDescription(),
                'editor' => $game->getEditor(),
                'hasCategory' => $game->getHasCategory()->map(fn($category) => $category->getName())->toArray(),
                'hasTag' => $game->getHasTag()->map(fn($tag) => $tag->getName())->toArray(),

            ];

            $serializedGames[] = $serializedGame;
        }

        // Sérialiser en JSON en utilisant SerializerInterface
        $serializedData = $serializer->serialize($serializedGames, 'json', [
            'groups' => 'game_browse',
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($serializedData, 200, [], true);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show(int $id, GameRepository $gameRepository, SerializerInterface $serializer): JsonResponse
    {
        $game = $gameRepository->find($id);
        if (!$game) {
            return new JsonResponse(['message' => 'Game not found'], 404);
        }

        // Sérialiser le game
        $serializedGame = [
            'id' => $game->getId(),
            'name' => $game->getName(),
            'releaseDate' => $game->getReleaseDate()->format('Y-m-d'),
            'createdAt' => $game->getCreatedAt()->format('Y-m-d H:i:s'),
            'picture' => $game->getPicture(),
            'price' => $game->getPrice(),
            'description' => $game->getDescription(),
            'editor' => $game->getEditor(),
            'hasCategory' => $game->getHasCategory()->map(fn($category) => $category->getName())->toArray(),
            'hasTag' => $game->getHasTag()->map(fn($tag) => $tag->getName())->toArray(),
        ];

        // Sérialiser en JSON en utilisant SerializerInterface
        $serializedData = $serializer->serialize($serializedGame, 'json', [
            'groups' => 'game_show',
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        // Retourner les données en JSON
        return new JsonResponse($serializedData, 200, [], true);
    }

    

}
    

