<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Entity\UserGameKey;
use App\Entity\ValidateOrder;
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
        $order = $orderRepository->findCurrentOrderByUser($user);

        // Si aucun panier actif n'est trouvé, en créer un nouveau
        if (!$order) {
            $order = new Order();
            $order->setUser($user);
            $order->setStatus('pending');
            $order->setCreatedAt(new \DateTimeImmutable());
        }

        // Ajouter le jeu au panier avec la quantité spécifiée
        $order->addGame($game, $quantity);

        // Calculer le total du panier en fonction des jeux et de leurs quantités
        $total = 0;
        foreach ($order->getGames() as $gameInOrder) {
            $total += $gameInOrder->getPrice() * $order->getQuantity($gameInOrder);
        }

        // Mettre à jour le total du panier
        $order->setTotal($total);

        // Persister et enregistrer les modifications dans la base de données
        $entityManager->persist($order);
        $entityManager->flush();

        // Construire la réponse JSON avec les détails du panier
        $cart = [];
        foreach ($order->getGames() as $gameInOrder) {
            $cart[] = [
                'id' => $gameInOrder->getId(),
                'name' => $gameInOrder->getName(),
                'price' => $gameInOrder->getPrice(),
                'quantity' => $order->getQuantity($gameInOrder),
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
        $quantity = $data['quantity'] ?? 1; // Quantité par défaut à retirer : 1

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
        $order = $orderRepository->findCurrentOrderByUser($user);

        // Vérifier si un panier actif existe
        if (!$order) {
            return new JsonResponse(['error' => 'Aucun panier actif trouvé'], 400);
        }

        // Vérifier si le jeu est présent dans le panier
        if (!$order->getGames()->contains($game)) {
            return new JsonResponse(['error' => 'Le jeu n\'est pas dans le panier'], 400);
        }

        // Réduire la quantité spécifiée du jeu dans le panier
        $currentQuantity = $order->getQuantity($game);
        if ($quantity >= $currentQuantity) {
            // Si la quantité à retirer est supérieure ou égale à la quantité actuelle, retirer le jeu complètement
            $order->removeGame($game);
        } else {
            // Sinon, juste réduire la quantité
            $order->setQuantity([$game->getId() => $currentQuantity - $quantity]);
        }

        // Recalculer le total
        $total = 0.0;
        foreach ($order->getGames() as $gameInOrder) {
            $total += $gameInOrder->getPrice() * $order->getQuantity($gameInOrder);
        }
        $order->setTotal($total);

        // Vérifier si le panier est vide et le supprimer si nécessaire
        if ($order->getGames()->isEmpty()) {
            $entityManager->remove($order);
        } else {
            $entityManager->persist($order);
        }

        $entityManager->flush();

        // Construire la réponse JSON avec les détails du panier et le nouveau total
        $cartDetails = [];
        foreach ($order->getGames() as $gameInOrder) {
            $cartDetails[] = [
                'id' => $gameInOrder->getId(),
                'name' => $gameInOrder->getName(),
                'price' => $gameInOrder->getPrice(),
                'quantity' => $order->getQuantity($gameInOrder),
            ];
        }

        return new JsonResponse(['message' => 'Jeu retiré du panier', 'cart' => $cartDetails, 'total' => $order->getTotal()], 200);
    }
    #[Route('/clear', name: 'clear_cart', methods: ['POST'])]
    public function clearCart(Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Rechercher un panier en cours pour l'utilisateur
        $order = $orderRepository->findCurrentOrderByUser($user);

        // Vérifier si un panier actif existe
        if (!$order) {
            return new JsonResponse(['error' => 'Aucun panier actif trouvé'], 400);
        }

        // Supprimer tous les jeux du panier
        foreach ($order->getGames() as $game) {
            $order->removeGame($game); // Appel correct avec le jeu à supprimer
        }

        // Supprimer le panier s'il est vide
        if ($order->getGames()->isEmpty()) {
            $entityManager->remove($order);
        } else {
            $entityManager->persist($order);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Panier vidé avec succès'], 200);
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
    $order = $orderRepository->findCurrentOrderByUser($user);
    if (!$order || $order->getGames()->isEmpty()) {
        return new JsonResponse(['error' => 'Le panier est vide'], 400);
    }

    // Créer une nouvelle instance de ValidateOrder
    $validateOrder = new ValidateOrder();
    $validateOrder->setUsers($user); // Associer l'utilisateur au ValidateOrder
    $validateOrder->setQuantity($order->getGames()->count());
    $validateOrder->setTotalPrice($order->getTotal());
    $validateOrder->setCreatedAt(new \DateTimeImmutable());

    // Générer et associer des clés aléatoires aux jeux
    foreach ($order->getGames() as $game) {
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

    // Transférer les jeux de l'Order à ValidateOrder
    foreach ($order->getGames() as $game) {
        $validateOrder->addGame($game);
    }

    // Associer l'Order à ValidateOrder
    $validateOrder->addOrder($order);

    // Persiste l'entité ValidateOrder
    $entityManager->persist($validateOrder);

    // Supprimer tous les jeux du panier actuel
    foreach ($order->getGames() as $game) {
        $order->removeGame($game);
    }
    $entityManager->remove($order); // Optionnellement supprimer toute l'entité Order

    // Flush des changements
    $entityManager->flush();

    return new JsonResponse(['message' => 'Commande complétée avec succès'], 200);
}

// Méthode pour générer une clé aléatoire de jeu
private function generateActivationKey(): string
{
    return bin2hex(random_bytes(16)); // Exemple de clé hexadécimale de 16 octets
}
}
