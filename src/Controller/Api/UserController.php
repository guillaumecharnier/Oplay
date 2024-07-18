<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user', name: 'app_api_user_')]
class UserController extends AbstractController
{

    // #[Route('/', name: 'browse', methods: ['GET'])]
    // public function browse(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    // {
    //     // Récupérer tous les utilisateurs depuis le repository
    //     $users = $userRepository->findAll();

    //     // Sérialiser les utilisateurs en utilisant SerializerInterface
    //     $serializedData = $serializer->serialize($users, 'json', [
    //         'groups' => 'user_browse',
    //         'circular_reference_handler' => function ($object) {
    //             return $object->getId();
    //         }
    //     ]);

    //     // Retourner la réponse JSON contenant les utilisateurs sérialisés
    //     return new JsonResponse($serializedData, Response::HTTP_OK, [], true);
    // }

    #[Route('/detail', name: 'show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Si l'utilisateur n'est pas trouvé, retourner une réponse JSON avec un message d'erreur
        if (is_null($user)) {
            $info = [
                'success' => false,
                'error_message' => 'Utilisateur non trouvé',
                'error_code' => 'User_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        // Retourner les détails de l'utilisateur sous forme de JSON avec le groupe 'user_show'
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user_show']);
    }

    #[Route('/edit', name: 'edit', methods: ['PATCH'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {

        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Décoder les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        // Mettre à jour les informations de l'utilisateur
        if (isset($data['firstname'])) {
            $user->setFirstname($data['firstname']);
        }
        if (isset($data['lastname'])) {
            $user->setLastname($data['lastname']);
        }
        if (isset($data['nickname'])) {
            $user->setNickname($data['nickname']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['picture'])) {
            $user->setPicture($data['picture']);
        }
        if (isset($data['password'])) {
            // Hasher le nouveau mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }

        // Valider les données utilisateur
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new JsonResponse(['error' => $errorsString], 400);
        }

        // Persister les données utilisateur mises à jour
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur mis à jour avec succès'], 200);
    }
    
    #[Route('/tags', name: 'update_tags', methods: ['POST'])]
    public function updateTags(
        Request $request,
        EntityManagerInterface $entityManager,
        TagRepository $tagRepository
    ): JsonResponse {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Prendre les données JSON de la requête
        $data = json_decode($request->getContent(), true);
        if (!isset($data['tag_ids']) || !is_array($data['tag_ids'])) {
            return new JsonResponse(['error' => 'Liste des IDs de tags manquante ou incorrecte'], 400);
        }

        // Récupérer les tags actuels de l'utilisateur
        $currentTags = $user->getPreferedTag();

        // Supprimer les tags actuels de l'utilisateur
        foreach ($currentTags as $currentTag) {
            $user->removePreferedTag($currentTag);
        }

        // Ajouter les nouveaux tags à l'utilisateur
        foreach ($data['tag_ids'] as $tagId) {
            $tag = $tagRepository->find($tagId);
            if ($tag) {
                $user->addPreferedTag($tag);
            } else {
                return new JsonResponse(['error' => "Tag avec ID $tagId non trouvé"], 404);
            }
        }

        // Enregistrer les modifications
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Tags mis à jour avec succès'], 200);
    }

    #[Route('/categories', name: 'update_category', methods: ['POST'])]
    public function updateCategory(
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryRepository $CategoryRepository
    ): JsonResponse {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Prendre les données JSON de la requête
        $data = json_decode($request->getContent(), true);
        if (!isset($data['category_ids']) || !is_array($data['category_ids'])) {
            return new JsonResponse(['error' => 'Liste des IDs de categories manquante ou incorrecte'], 400);
        }

        // Récupérer les categories actuels de l'utilisateur
        $currentTags = $user->getSelectedCategory();

        // Supprimer les categories actuels de l'utilisateur
        foreach ($currentTags as $currentTag) {
            $user->removeSelectedCategory($currentTag);
        }

        // Ajouter les nouvelles categories à l'utilisateur
        foreach ($data['category_ids'] as $categoryId) {
            $tag = $CategoryRepository->find($categoryId);
            if ($tag) {
                $user->addSelectedCategory($tag);
            } else {
                return new JsonResponse(['error' => "Category avec ID $categoryId non trouvé"], 404);
            }
        }

        // Enregistrer les modifications
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Categorie mis a jour avec succes'], 200);
    }

    #[Route('/theme', name: 'update_theme', methods: ['POST'])]
    public function updateTheme(
        Request $request,
        EntityManagerInterface $entityManager,
        ThemeRepository $themeRepository
    ): JsonResponse {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Prendre les données JSON de la requête
        $data = json_decode($request->getContent(), true);
        if (!isset($data['theme_id'])) {
            return new JsonResponse(['error' => 'ID de thème manquant'], 400);
        }

        // Récupérer le thème actuel de l'utilisateur (s'il en a déjà un)
        $currentTheme = $user->getChooseTheme();

        // Si l'utilisateur a déjà un thème, le retirer
        if ($currentTheme !== null) {
            $user->setChooseTheme(null);
        }

        // Récupérer le thème sélectionné par ID
        $themeId = $data['theme_id'];
        $theme = $themeRepository->find($themeId);
        if (!$theme) {
            return new JsonResponse(['error' => "Thème avec l'ID $themeId non trouvé"], 404);
        }

        // Associer le nouveau thème à l'utilisateur
        $user->setChooseTheme($theme);

        // Enregistrer les modifications
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Thème mis à jour avec succès'], 200);
    }
    
}