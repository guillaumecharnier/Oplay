<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/user', name: 'app_api_user', methods: ['GET'])]
class UserController extends AbstractController
{
    #[Route('/browse', name: 'browse', methods: ['GET'])]
    public function users(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        // Transform the user into an array
        $userData = [];
        foreach ($users as $user) {
            $chooseThemeId = $user->getChooseTheme() ? $user->getChooseTheme()->getId() : null;
            $userData[] = [
                'id' => $user->getId(),
                'choose_theme_id' => $chooseThemeId,
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'nickname' => $user->getNickname(),
                'picture' => $user->getPicture(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }

        // Return the data in JSON
        return $this->json(['users' => $userData], 200, [], ["groups" => "user_browse"]);
    }   



    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function user(int $id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $chooseThemeId = $user->getChooseTheme() ? $user->getChooseTheme()->getId() : null;

        // Transform the user into an array
        $userData = [
            'id' => $user->getId(),
            'choose_theme_id' =>$chooseThemeId,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'nickname' => $user->getNickname(),
            'picture' => $user->getPicture(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        // Return the data in JSON
        return $this->json(['user' => $userData], 200, [], ["groups" => "user_show"]);

    }
}
