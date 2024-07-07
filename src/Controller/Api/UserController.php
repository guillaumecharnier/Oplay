<?php

namespace App\Controller\Api;

use App\Entity\User;
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

#[Route('/api/user', name: 'app_api_user_', methods: ['GET'])]
class UserController extends AbstractController
{

    #[Route('/browse', name: 'browse', methods: ['GET'])]
    public function browse(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();

        // Sérialiser les utilisateurs en utilisant SerializerInterface
        $serializedData = $serializer->serialize($users, 'json', [
            'groups' => 'user_browse',
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($serializedData, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}/show', name: 'app_api_category_show', methods: ['GET'])]
    public function show($id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (is_null($user)) {
            $info = [
                'success' => false,
                'error_message' => 'Utilisateur non trouvée',
                'error_code' => 'User_not_found',
            ];
            return $this->json($info, Response::HTTP_NOT_FOUND);
        }

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user_show']);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
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

    #[Route('/{id}/edit', name: 'edit', methods: ['PATCH'])]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        // Fetch the user from the database
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Decode the JSON data from the request
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        // Update user information
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
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }

        // Validate the user data
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new JsonResponse(['error' => $errorsString], 400);
        }

        // Persist the updated user data
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User updated successfully'], 200);
    }
    
}
