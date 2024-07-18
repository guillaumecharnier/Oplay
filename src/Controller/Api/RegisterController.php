<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/public/api/register', name: 'app_api_user_')]
class RegisterController extends AbstractController
{

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        // Prendre les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        // Créer le nouvel utilisateur
        $user = new User();
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setNickname($data['nickname']);
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']);

        // Télécharger l'image de profil si l'utilisateur a défini une image
        if (!empty($data['picture'])) {
            $pictureFile = base64_decode($data['picture']);
            if ($pictureFile) {
                $pictureFileName = md5(uniqid()) . '.jpg' . '.png'; 
                file_put_contents($this->getParameter('pictures_directory') . '/' . $pictureFileName, $pictureFile);
                $user->setPicture($pictureFileName);
            } else {
                return new JsonResponse(['error' => 'Invalid picture format'], 400);
            }
        }

        // Hasher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Valider les données utilisateur
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new JsonResponse(['error' => $errorsString], 400);
        }

        // Enregistrer les données utilisateur
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur créé avec succès'], 201);
    }
    
}