<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Repository\ValidateOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_users_index', methods: ['GET'])]
    public function users(UserRepository $userRepository,ValidateOrderRepository $validateOrderRepository): Response
    {
        $users = $userRepository->findAll();

        $validateOrders = $validateOrderRepository->findAll();
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
            ];
        }

        // Return the data in JSON
        return $this->json([
            'user' => $userData,
            'validateOrders' => $validateOrders,
        ]);
    }


    #[Route('/user/{id}', name: 'app_user_index', methods: ['GET'])]
    public function user(int $id, UserRepository $userRepository, ValidateOrderRepository $validateOrderRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $validateOrders = $validateOrderRepository->findAll();

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
        ];

        // Return the data in JSON
        return $this->json([
            'user' => $userData,
            'validateOrders' => $validateOrders,
        ]);
    }
}
