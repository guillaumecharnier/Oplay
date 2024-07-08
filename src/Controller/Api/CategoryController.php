<?php

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/category', name: 'app_api_category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'browse', methods: "GET")]
    public function browse(CategoryRepository $categoryRepository): JsonResponse
    {
        // Récupérer toutes les catégories depuis le repository
        $allGenres = $categoryRepository->findAll();
        
        // Retourner les catégories sous forme de JSON avec le groupe 'category_browse'
        return $this->json($allGenres, Response::HTTP_OK, [], ["groups" => "category_browse"]);
    }

    #[Route('/{id}', name: 'app_api_category_show', methods: ['GET'])]
    public function show($id, CategoryRepository $categoryRepository): JsonResponse
    {
        // Trouver la catégorie spécifiée par son ID
        $category = $categoryRepository->find($id);

        // Si la catégorie n'est pas trouvée, retourner une réponse JSON avec un message d'erreur
        if (is_null($category)) {
            $info = [
                'success' => false,
                'error_message' => 'Category non trouvée',
                'error_code' => 'Category_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        // Retourner les détails de la catégorie sous forme de JSON avec le groupe 'category_show'
        return $this->json($category, Response::HTTP_OK, [], ['groups' => 'category_show']);
    }

    #[Route('/{categoryId}/games', name: 'category_games', methods: ['GET'])]
    public function getCategoryGames(CategoryRepository $categoryRepository, SerializerInterface $serializer, $categoryId): JsonResponse
    {
        // Trouver la catégorie spécifiée par son ID
        $category = $categoryRepository->find($categoryId);

        // Si la catégorie n'est pas trouvée, retourner une réponse JSON avec un message d'erreur
        if (is_null($category)) {
            $info = [
                'success' => false,
                'error_message' => 'Category non trouvée',
                'error_code' => 'Category_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        // Récupérer les jeux associés à cette catégorie
        $games = $category->getGames();

        // Sérialiser les jeux en utilisant SerializerInterface avec le groupe 'game_browse'
        $serializedData = $serializer->serialize($games, 'json', [
            'groups' => 'game_browse',
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        // Retourner les jeux sérialisés sous forme de JsonResponse
        return new JsonResponse($serializedData, Response::HTTP_OK, [], true);
    }
}
