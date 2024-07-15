<?php

namespace App\Controller\Backoffice;

use App\Entity\Game;
use App\Entity\GameOrder;
use App\Entity\UserGameKey;
use App\Form\GameType;
use App\Repository\GameOrderRepository;
use App\Repository\GameRepository;
use App\Service\GameKeyService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/back/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'app_game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository): Response
    {
        return $this->render('backoffice/game/index.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_show', methods: ['GET'])]
    public function show(Game $game): Response
    {
        return $this->render('backoffice/game/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_delete', methods: ['POST'])]
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $game->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/generate-key', name: 'app_game_generate_key_user', methods: ['GET','POST'])]
    public function generateGameKeyUser(UserGameKey $userGameKey, GameKeyService $gameKeyService): Response
    {
        $gameKeyService = $gameKeyService->generateNewKey($userGameKey);

        $user = $userGameKey->getUser();

        // Ajout d'un message flash pour afficher un message de succès
        $this->addFlash('success', 'Nouvelle clé générée avec succès !');

        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
    }

    #[Route('/{id}/generate-key', name: 'app_game_generate_key_order', methods: ['GET', 'POST'])]
    public function generateGameKeyOrder(
        UserGameKey $userGameKey, 
        GameKeyService $gameKeyService, 
        EntityManagerInterface $entityManager,
        GameOrderRepository $gameOrderRepository
    ): Response {
        // Générer une nouvelle clé de jeu
        $newKey = $gameKeyService->generateNewKey();

        // Mettre à jour l'objet UserGameKey avec la nouvelle clé
        $userGameKey->setGameKey($newKey);

        // Sauvegarder les changements dans la base de données
        $entityManager->persist($userGameKey);
        $entityManager->flush();

        // Récupérer la commande associée à cette clé de jeu
        $gameOrder = $gameOrderRepository->findOneBy([
            'game' => $userGameKey->getGame(),
            'order.user' => $userGameKey->getUser(),
        ]);

        if (!$gameOrder) {
            throw $this->createNotFoundException('The game order does not exist');
        }

        $order = $gameOrder->getOrder();

        // Ajout d'un message flash pour afficher un message de succès
        $this->addFlash('success', 'Nouvelle clé générée avec succès !');

        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }
}
    

