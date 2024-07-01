<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GameRepository; // Importez le repository si nécessaire
use Symfony\Component\HttpFoundation\Response;

class GameController extends AbstractController
{
    #[Route('/game', name: 'app_genre_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository): Response
    {
        return $this->render('game.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }
}
