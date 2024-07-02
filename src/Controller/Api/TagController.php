<?php

namespace App\Controller\Api;

use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api/tag', name: 'app_api_tag')]
class TagController extends AbstractController
{
    #[Route('/browse', name: 'browse', methods: "GET")]
    public function browse(TagRepository $tagRepository): JsonResponse
    {
        $allTags = $tagRepository->findAll();
        
        return $this->json($allTags, 200, [], ["groups" => "tag_browse"]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function showCategory($id, TagRepository $tagRepository): JsonResponse
    {
        $tag= $tagRepository->find($id);

        if (is_null($tag)) {
            $info = [
                'success' => false,
                'error_message' => 'Category non trouvée',
                'error_code' => 'Category_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        // Traiter le cas où la catégorie est trouvée
        return $this->json($tag, Response::HTTP_OK, [], ['groups' => 'tag_show']);
    }

}

