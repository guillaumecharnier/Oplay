<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Repository\GameRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/order', name: 'app_api_order_')]
class OrderController extends AbstractController
{
  
    #[Route('/add-to-cart', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(Request $request, GameRepository $gameRepository, OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $gameId = $data['game_id'] ?? null;

        if (!$gameId) {
            return new JsonResponse(['error' => 'Game ID is required'], 400);
        }

        $game = $gameRepository->find($gameId);
        if (!$game) {
            return new JsonResponse(['error' => 'Game not found'], 404);
        }

        // Recherche d'un panier en cours pour l'utilisateur
        $order = $orderRepository->findCurrentOrderByUser($user);

        if (!$order) {
            // Si aucun panier en cours, en créer un nouveau
            $order = new Order();
            $order->setUser($user);
            $order->setStatus('pending');
            $order->setCreatedAt(new \DateTimeImmutable());
        }

        // Ajouter le jeu au panier
        $order->addGame($game);

        // Calculer le prix total des jeux dans le panier
        $total = 0;
        foreach ($order->getGames() as $gameInOrder) {
            $total += $gameInOrder->getPrice();
        }

        // Mettre à jour le total dans l'Order
        $order->setTotal($total);

        // Persiste et flush l'entité Order
        $entityManager->persist($order);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Game added to cart'], 200);
    }


    #[Route('/remove-from-cart', name: 'remove_from_cart', methods: ['POST'])]
    public function removeFromCart(Request $request, GameRepository $gameRepository, OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $gameId = $data['game_id'] ?? null;

        if (!$gameId) {
            return new JsonResponse(['error' => 'Game ID is required'], 400);
        }

        $game = $gameRepository->find($gameId);
        if (!$game) {
            return new JsonResponse(['error' => 'Game not found'], 404);
        }

        $order = $orderRepository->findCurrentOrderByUser($user);
        if (!$order) {
            return new JsonResponse(['error' => 'No active order found'], 400);
        }

        $order->removeGame($game);
        $entityManager->persist($order);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Game removed from cart'], 200);
    }

    #[Route('/view-cart', name: 'view_cart', methods: ['GET'])]
    public function viewCart(OrderRepository $orderRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $order = $orderRepository->findCurrentOrderByUser($user);
        if (!$order) {
            return new JsonResponse(['message' => 'Cart is empty'], 200);
        }

        $cart = [];
        foreach ($order->getGames() as $game) {
            $cart[] = [
                'id' => $game->getId(),
                'name' => $game->getName(),
                'price' => $game->getPrice(),
            ];
        }

        return new JsonResponse(['cart' => $cart], 200);
    }

    #[Route('/checkout', name: 'checkout', methods: ['POST'])]
    public function checkout(OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $order = $orderRepository->findCurrentOrderByUser($user);
        if (!$order || $order->getGames()->isEmpty()) {
            return new JsonResponse(['error' => 'Cart is empty'], 400);
        }

        $order->setStatus('completed');
        $entityManager->persist($order);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Order completed successfully'], 200);
    }
}
