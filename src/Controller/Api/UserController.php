<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user', name: 'app_api_user', methods: ['GET'])]
class UserController extends AbstractController
{

    #[Route('/browse', name: 'browse', methods: ['GET'])]
    public function users(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();

        // Sérialiser les utilisateurs
        $serializedUsers = [];
        foreach ($users as $user) {
            $serializedUser = [
                'id' => $user->getId(),
                'choose_theme_id' => $user->getChooseTheme() ? $user->getChooseTheme()->getId() : null,
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'nickname' => $user->getNickname(),
                'picture' => $user->getPicture(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'selectedCategory' => $user->getSelectedCategory()->map(fn($category) => $category->getName())->toArray(),
                'preferedTag' => $user->getPreferedTag()->map(fn($tag) => $tag->getName())->toArray(),
                'purchasedOrderIds' => $user->getPurchasedOrder()->map(fn($purchasedOrder) => $purchasedOrder->getId())->toArray(),
                'userGetGame' => $user->getUserGetGame()->map(fn($userGetGame) => $userGetGame->getName())->toArray(),
                'userGameKeys' => $user->getUserGameKeys()->map(fn($userGameKeys) => $userGameKeys->getGameKey())->toArray(),

            ];

            $serializedUsers[] = $serializedUser;
        }

        // Sérialiser en JSON en utilisant SerializerInterface
        $serializedData = $serializer->serialize($serializedUsers, 'json', [
            'groups' => 'user_browse',
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($serializedData, 200, [], true);
    }

  

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function showCategory($id, UserRepository $userRepository): JsonResponse
    {
        $user= $userRepository->find($id);

        if (is_null($user)) {
            $info = [
                'success' => false,
                'error_message' => 'Utilisateur non trouvée',
                'error_code' => 'Utilisateur_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        // Traiter le cas où le user est trouvée
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user_show']);
    }
    
}
