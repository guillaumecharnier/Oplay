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

        // Créer un nouveau panier s'il n'existe pas déjà
        if (!$order) {
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

        return new JsonResponse(['message' => 'Jeu ajouté au panier'], 200);
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
        $order = $orderRepository->findCurrentOrderByUser($user);

        // Vérifier si un panier actif existe
        if (!$order) {
            return new JsonResponse(['error' => 'Aucun panier actif trouvé'], 400);
        }

        // Vérifier si le jeu est présent dans le panier
        if (!$order->getGames()->contains($game)) {
            return new JsonResponse(['error' => 'Le jeu n\'est pas dans le panier'], 400);
        }

        // Supprimer le jeu du panier
        $order->removeGame($game);

        // Recalculer le total
        $total = 0.0;
        foreach ($order->getGames() as $gameInOrder) {
            $total += $gameInOrder->getPrice(); // Adapter en fonction de votre logique de calcul du total
        }
        $order->setTotal($total);

        // Vérifier si le panier est vide et le supprimer si nécessaire
        if ($order->getGames()->isEmpty()) {
            $entityManager->remove($order);
        } else {
            $entityManager->persist($order);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Jeu retiré du panier'], 200);
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
        $validateOrder->setQuantity($order->getGames()->count());
        $validateOrder->setTotalPrice($order->getTotal());

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
