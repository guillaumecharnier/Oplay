<?php

namespace App\Controller\Api;

use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/tag', name: 'app_api_tag_')]
class TagController extends AbstractController
{
    #[Route('/browse', name: 'browse', methods: "GET")]
    public function browse(TagRepository $tagRepository): JsonResponse
    {
        $allTags = $tagRepository->findAll();
        
        return $this->json($allTags, Response::HTTP_OK, [], ["groups" => "tag_browse"]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show($id, TagRepository $tagRepository): JsonResponse
    {
        $tag= $tagRepository->find($id);

        if (is_null($tag)) {
            $info = [
                'success' => false,
                'error_message' => 'Tag non trouvé',
                'error_code' => 'Tag_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        return $this->json($tag, Response::HTTP_OK, [], ['groups' => 'tag_show']);
    }

    #[Route('/{tagId}/games', name: 'tag_games', methods: ['GET'])]
    public function getTagGame(TagRepository $tagRepository, SerializerInterface $serializer, $tagId): JsonResponse
    {
        $tag = $tagRepository->find($tagId);

        if (is_null($tag)) {
            $info = [
                'success' => false,
                'error_message' => 'Tag non trouvée',
                'error_code' => 'Tag_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        // Récupérer les jeux associés à ce tag
        $tags = $tag->getGames();

        // Sérialiser les jeux en utilisant SerializerInterface
        $serializedData = $serializer->serialize($tags, 'json', [
            'groups' => 'game_browse',
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($serializedData, Response::HTTP_OK, [], true);
    }

}

