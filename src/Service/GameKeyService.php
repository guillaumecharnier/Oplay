<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserGameKey;

class GameKeyService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generateNewKey(UserGameKey $userGameKey): string
    {
        $newKey = bin2hex(random_bytes(16)); // Génération d'une nouvelle clé aléatoire
        $userGameKey->setGameKey($newKey);

        // Enregistrement des modifications en base de données
        $this->entityManager->persist($userGameKey);
        $this->entityManager->flush();

        return $newKey;
    }
}
