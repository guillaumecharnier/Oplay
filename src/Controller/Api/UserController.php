<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/api/user', name: 'app_api_user', methods: ['GET'])]
class UserController extends AbstractController
{
    #[Route('/browse', name: 'browse', methods: ['GET'])]
    public function users(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        // Transform the user into an array
        $userData = [];
        foreach ($users as $user) {
            $chooseThemeId = $user->getChooseTheme() ? $user->getChooseTheme()->getId() : null;

            $selectedCategoryId = $user->getSelectedCategory()->map(function($category) {
                return $category->getId();
            })->toArray();

            $preferedTagId = $user->getPreferedTag()->map(function($tag) {
                return $tag->getId();
            })->toArray();

            $userData[] = [
                'id' => $user->getId(),
                'choose_theme_id' => $chooseThemeId,
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'nickname' => $user->getNickname(),
                'picture' => $user->getPicture(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'selectedCategoryId' => $selectedCategoryId,
                'preferedTagId' => $preferedTagId,
            ];
        }

        // Return the data in JSON
        return $this->json(['users' => $userData], 200, [], ["groups" => "user_browse"]);
    }   



    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function user(int $id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $chooseThemeId = $user->getChooseTheme() ? $user->getChooseTheme()->getId() : null;

        $selectedCategoryId = $user->getSelectedCategory()->map(function($category) {
            return $category->getId();
        })->toArray();

        $preferedTagId = $user->getPreferedTag()->map(function($tag) {
            return $tag->getId();
        })->toArray();

        // Transform the user into an array
        $userData = [
            'id' => $user->getId(),
            'choose_theme_id' =>$chooseThemeId,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'nickname' => $user->getNickname(),
            'picture' => $user->getPicture(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'selectedCategoryId' => $selectedCategoryId,
            'preferedTagId' => $preferedTagId,
        ];

        // Return the data in JSON
        return $this->json(['user' => $userData], 200, [], ["groups" => "user_show"]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    ): JsonResponse {
        // Take the Json data from the request
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        // Create the new user
        $user = new User();
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setNickname($data['nickname']);
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']);

        // Upload the picture if the user set a profil picture
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

        // Hash the password
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Valide if the data 
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new JsonResponse(['error' => $errorsString], 400);
        }

        // Register the data
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User creaed successfully'], 201);
    }
}
