<?php

 namespace App\Controller;

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
         $validateOrders = $validateOrderRepository->findAll();

         return $this->render('user.html.twig', [
             'users' => $users,
             'validateOrders' => $validateOrders,
         ]);
     }
 }