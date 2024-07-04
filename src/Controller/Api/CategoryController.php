<?php

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api/category', name: 'app_api_category_')]
class CategoryController extends AbstractController
{
    #[Route('/browse', name: 'browse', methods: "GET")]
    public function browse(CategoryRepository $categoryRepository): JsonResponse
    {
        $allGenres = $categoryRepository->findAll();
        
        return $this->json($allGenres, 200, [], ["groups" => "category_browse"]);
    }

    #[Route('/{id}/show', name: 'app_api_category_show', methods: ['GET'])]
    public function show($id, CategoryRepository $categoryRepository): JsonResponse
    {
        $category = $categoryRepository->find($id);

        if (is_null($category)) {
            $info = [
                'success' => false,
                'error_message' => 'Category non trouvée',
                'error_code' => 'Category_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        // Traiter le cas où la catégorie est trouvée
        // Par exemple, retourner les données de la catégorie
        return $this->json($category, Response::HTTP_OK, [], ['groups' => 'category_show']);
    }

}

