<?php

namespace App\Controller\Api;

use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/public/api/tag', name: 'app_api_tag_')]
class TagController extends AbstractController
{
    #[Route('/', name: 'browse', methods: "GET")]
    public function browse(TagRepository $tagRepository): JsonResponse
    {
        // Récupérer tous les tags depuis le repository
        $allTags = $tagRepository->findAll();
        
        // Retourner tous les tags sous forme de JSON avec le groupe 'tag_browse'
        return $this->json($allTags, Response::HTTP_OK, [], ["groups" => "tag_browse"]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show($id, TagRepository $tagRepository): JsonResponse
    {
        // Trouver le tag spécifié par son ID
        $tag = $tagRepository->find($id);

        // Si le tag n'est pas trouvé, retourner une réponse JSON avec un message d'erreur
        if (is_null($tag)) {
            $info = [
                'success' => false,
                'error_message' => 'Tag non trouvé',
                'error_code' => 'Tag_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        // Retourner les détails du tag sous forme de JSON avec le groupe 'tag_show'
        return $this->json($tag, Response::HTTP_OK, [], ['groups' => 'tag_show']);
    }

    #[Route('/{tagId}/games', name: 'tag_games', methods: ['GET'])]
    public function getTagGame(TagRepository $tagRepository, SerializerInterface $serializer, $tagId): JsonResponse
    {
        // Trouver le tag spécifié par son ID
        $tag = $tagRepository->find($tagId);

        // Si le tag n'est pas trouvé, retourner une réponse JSON avec un message d'erreur
        if (is_null($tag)) {
            $info = [
                'success' => false,
                'error_message' => 'Tag non trouvée',
                'error_code' => 'Tag_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        // Récupérer les jeux associés à ce tag
        $games = $tag->getGames();

        // Sérialiser les jeux en utilisant SerializerInterface
        $serializedData = $serializer->serialize($games, 'json', [
            'groups' => 'game_browse',
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        // Retourner les jeux associés à ce tag sous forme de JSON
        return new JsonResponse($serializedData, Response::HTTP_OK, [], true);
    }
}
