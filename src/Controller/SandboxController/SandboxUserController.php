<?php

 namespace App\Controller\SandboxController;

use App\Repository\UserRepository;
use App\Repository\ValidateOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

 use Symfony\Component\HttpFoundation\Response;

 class SandboxUserController extends AbstractController
 {
    #[Route('/user', name: 'app_user_index', methods: ['GET'])]
     public function index(UserRepository $userRepository,ValidateOrderRepository $validateOrderRepository): Response
     {
         $users = $userRepository->findAll();
         //dd($users);
         $validateOrders = $validateOrderRepository->findAll();

         // Forcer le chargement des thèmes choisis pour chaque utilisateur
        foreach ($users as $user) {
            if ($user->getChooseTheme() !== null) {
                $user->getChooseTheme()->getName(); // Accéder à la propriété pour forcer le chargement
            }
        }

         return $this->render('sandbox/sandboxUser.html.twig', [
             'users' => $users,
             'validateOrders' => $validateOrders,
         ]);
     }
 }