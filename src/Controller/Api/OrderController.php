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
        }

        // Ajouter le jeu au panier avec la quantité spécifiée
        $gameOrder = new GameOrder();
        $gameOrder->setGame($game);
        $gameOrder->setOrder($order);
        $gameOrder->setQuantity($quantity);
        $gameOrder->setTotalPrice($game->getPrice() * $quantity);

        $order->addGameOrder($gameOrder);

        // Calculer le total du panier en fonction des jeux et de leurs quantités
        $total = 0;
        foreach ($order->getGameOrders() as $gameOrder) {
            $total += $gameOrder->getTotalPrice();
        }

        // Mettre à jour le total du panier
        $order->setTotal($total);

        // Persister et enregistrer les modifications dans la base de données
        $entityManager->persist($order);
        $entityManager->persist($gameOrder);
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

        // Si aucun panier actif n'est trouvé, retourner une erreur
        if (!$order) {
            return new JsonResponse(['error' => 'Aucune commande en cours trouvée'], 404);
        }

        // Rechercher l'association GameOrder correspondant au jeu dans la commande
        $gameOrder = null;
        foreach ($order->getGameOrders() as $go) {
            if ($go->getGame()->getId() === $gameId) {
                $gameOrder = $go;
                break;
            }
        }

        if (!$gameOrder) {
            return new JsonResponse(['error' => 'Jeu non trouvé dans le panier'], 404);
        }

        // Supprimer le jeu du panier
        $order->removeGameOrder($gameOrder);

        // Recalculer le total du panier en fonction des jeux et de leurs quantités
        $total = 0;
        foreach ($order->getGameOrders() as $go) {
            $total += $go->getTotalPrice();
        }

        // Mettre à jour le total du panier
        $order->setTotal($total);

        // Persister et enregistrer les modifications dans la base de données
        $entityManager->persist($order);
        $entityManager->remove($gameOrder);
        $entityManager->flush();

        // Construire la réponse JSON avec les détails mis à jour du panier
        $cart = [];
        foreach ($order->getGameOrders() as $go) {
            $cart[] = [
                'id' => $go->getGame()->getId(),
                'name' => $go->getGame()->getName(),
                'price' => $go->getGame()->getPrice(),
                'quantity' => $go->getQuantity(),
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
    public function checkout(OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Rechercher le panier en cours pour l'utilisateur
        $order = $orderRepository->findOneBy([
            'user' => $user,
            'status' => 'pending'
        ]);

        if (!$order || $order->getGameOrders()->isEmpty()) {
            return new JsonResponse(['error' => 'Le panier est vide'], 400);
        }

        // Générer et associer des clés aléatoires aux jeux
        foreach ($order->getGameOrders() as $gameOrder) {
            $game = $gameOrder->getGame();
            $activationKey = $this->generateActivationKey();

            // Créer une instance de UserGameKey
            $userGameKey = new UserGameKey();
            $userGameKey->setUser($user);
            $userGameKey->setGame($game);
            $userGameKey->setGameKey($activationKey);
            $userGameKey->setCreatedAt(new \DateTimeImmutable());

            // Persiste l'entité UserGameKey
            $entityManager->persist($userGameKey);

            // Associer le jeu à l'utilisateur à travers la relation userGetGame de l'entité User
            $user->addUserGetGame($game);
        }

        // Mettre à jour le statut de la commande à 'validated'
        $order->setStatus('validated');

        // Persiste et enregistre les modifications dans la base de données
        $entityManager->persist($order);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Commande validée avec succès'], 200);
    }

    // Méthode pour générer une clé aléatoire de jeu
    private function generateActivationKey(): string
    {
        return bin2hex(random_bytes(16)); // Exemple de clé hexadécimale de 16 octets
    }
}
