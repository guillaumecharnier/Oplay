<?php

namespace App\Controller\Api;

use App\Entity\GameOrder;
use App\Entity\Order;
use App\Entity\UserGameKey;
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
  
    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addToCart(Request $request, GameRepository $gameRepository, OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);
        $gameId = $data['game_id'] ?? null;
        $quantity = $data['quantity'] ?? 1; // Quantité par défaut : 1

        // Vérifier si l'ID du jeu est présent dans la requête
        if (!$gameId) {
            return new JsonResponse(['error' => 'ID du jeu requis'], 400);
        }

        // Rechercher le jeu correspondant à l'ID
        $game = $gameRepository->find($gameId);
        if (!$game) {
            return new JsonResponse(['error' => 'Jeu non trouvé'], 404);
        }

        // Rechercher un panier en cours pour l'utilisateur
        $order = $orderRepository->findOneBy([
            'user' => $user,
            'status' => 'pending'
        ]);

        // Si aucun panier actif n'est trouvé, en créer un nouveau
        if (!$order) {
            $order = new Order();
            $order->setUser($user);
            $order->setStatus('pending');
            $order->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($order);
        }

        // Vérifier si le jeu est déjà dans le panier
        $existingGameOrder = $order->getGameOrders()->filter(function(GameOrder $gameOrder) use ($game) {
            return $gameOrder->getGame()->getId() === $game->getId();
        })->first();

        if ($existingGameOrder) {
            // Le jeu est déjà dans le panier, mettre à jour la quantité et le prix total
            $existingGameOrder->setQuantity($existingGameOrder->getQuantity() + $quantity);
            $existingGameOrder->setTotalPrice($existingGameOrder->getQuantity() * $game->getPrice());
            $entityManager->persist($existingGameOrder);
        } else {
            // Le jeu n'est pas dans le panier, ajouter une nouvelle entrée
            $gameOrder = new GameOrder();
            $gameOrder->setGame($game);
            $gameOrder->setOrder($order);
            $gameOrder->setQuantity($quantity);
            $gameOrder->setTotalPrice($game->getPrice() * $quantity);
            $order->addGameOrder($gameOrder);
            $entityManager->persist($gameOrder);
        }

        // Calculer le total du panier en fonction des jeux et de leurs quantités
        $total = 0;
        foreach ($order->getGameOrders() as $gameOrder) {
            $total += $gameOrder->getTotalPrice();
        }

        // Mettre à jour le total du panier
        $order->setTotal($total);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        // Construire la réponse JSON avec les détails du panier
        $cart = [];
        foreach ($order->getGameOrders() as $gameOrder) {
            $cart[] = [
                'id' => $gameOrder->getGame()->getId(),
                'name' => $gameOrder->getGame()->getName(),
                'price' => $gameOrder->getGame()->getPrice(),
                'quantity' => $gameOrder->getQuantity(),
            ];
        }

        return new JsonResponse([
            'message' => 'Jeu ajouté au panier',
            'cart' => $cart,
            'total' => $order->getTotal(),
        ], 200);
    }

    #[Route('/remove', name: 'remove', methods: ['POST'])]
    public function removeFromCart(Request $request, GameRepository $gameRepository, OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);
        $gameId = $data['game_id'] ?? null;

        // Vérifier si l'ID du jeu est présent dans la requête
        if (!$gameId) {
            return new JsonResponse(['error' => 'ID du jeu requis'], 400);
        }

        // Rechercher le jeu correspondant à l'ID
        $game = $gameRepository->find($gameId);
        if (!$game) {
            return new JsonResponse(['error' => 'Jeu non trouvé'], 404);
        }

        // Rechercher un panier en cours pour l'utilisateur
        $order = $orderRepository->findOneBy([
            'user' => $user,
            'status' => 'pending'
        ]);

        if (!$order) {
            return new JsonResponse(['error' => 'Aucun panier en cours trouvé'], 404);
        }

        // Rechercher l'objet GameOrder correspondant au jeu dans le panier
        $gameOrder = null;
        foreach ($order->getGameOrders() as $item) {
            if ($item->getGame()->getId() === $game->getId()) {
                $gameOrder = $item;
                break;
            }
        }

        if (!$gameOrder) {
            return new JsonResponse(['error' => 'Le jeu n\'est pas dans le panier'], 400);
        }

        // Si la quantité est supérieure à 1, diminuer la quantité
        if ($gameOrder->getQuantity() > 1) {
            $gameOrder->setQuantity($gameOrder->getQuantity() - 1);
            $gameOrder->setTotalPrice($game->getPrice() * $gameOrder->getQuantity());
        } else {
            // Si la quantité est 1, supprimer le jeu du panier
            $order->removeGameOrder($gameOrder);
            $entityManager->remove($gameOrder);
        }

        // Calculer le nouveau total du panier
        $total = 0;
        foreach ($order->getGameOrders() as $item) {
            $total += $item->getTotalPrice();
        }
        $order->setTotal($total);

        // Persister et enregistrer les modifications dans la base de données
        $entityManager->persist($order);
        $entityManager->flush();

        // Construire la réponse JSON avec les détails du panier
        $cart = [];
        foreach ($order->getGameOrders() as $item) {
            $cart[] = [
                'id' => $item->getGame()->getId(),
                'name' => $item->getGame()->getName(),
                'price' => $item->getGame()->getPrice(),
                'quantity' => $item->getQuantity(),
            ];
        }

        return new JsonResponse([
            'message' => 'Jeu retiré du panier',
            'cart' => $cart,
            'total' => $order->getTotal(),
        ], 200);
    }

    #[Route('/clear', name: 'clear', methods: ['POST'])]
    public function clearCart(OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Rechercher un panier en cours pour l'utilisateur
        $order = $orderRepository->findOneBy([
            'user' => $user,
            'status' => 'pending'
        ]);

        // Si aucun panier actif n'est trouvé, retourner une erreur
        if (!$order) {
            return new JsonResponse(['error' => 'Aucune commande en cours trouvée'], 404);
        }

        // Supprimer tous les GameOrder associés à la commande
        foreach ($order->getGameOrders() as $gameOrder) {
            $entityManager->remove($gameOrder);
        }

        // Supprimer la commande
        $entityManager->remove($order);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Panier supprimé avec succès'
        ], 200);
    }

    #[Route('/checkout', name: 'checkout', methods: ['POST'])]
    public function checkout(Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Rechercher un panier en cours pour l'utilisateur
        $order = $orderRepository->findOneBy([
            'user' => $user,
            'status' => 'pending'
        ]);

        if (!$order) {
            return new JsonResponse(['error' => 'Aucun panier en cours trouvé'], 404);
        }

        // Mettre à jour le statut de la commande à "validated"
        $order->setStatus('validated');
        $entityManager->persist($order);

        // Générer les clés de jeu pour chaque jeu de la commande
        foreach ($order->getGameOrders() as $gameOrder) {
            for ($i = 0; $i < $gameOrder->getQuantity(); $i++) {
                $userGameKey = new UserGameKey();
                $userGameKey->setUser($user);
                $userGameKey->setGame($gameOrder->getGame());
                $userGameKey->setGameKey($this->generateUniqueGameKey()); // Générer une clé de jeu unique
                $userGameKey->setCreatedAt(new \DateTimeImmutable());
                $entityManager->persist($userGameKey);
            }
        }

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        return new JsonResponse(['message' => 'Commande validée'], 200);
    }

    /**
     * Génère une clé de jeu unique.
     */
    private function generateUniqueGameKey(): string
    {
        // Logique pour générer une clé de jeu unique
        return bin2hex(random_bytes(16));
    }
}
