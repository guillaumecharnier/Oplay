<?php

namespace App\Entity;

use App\Repository\UserGameKeyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserGameKeyRepository::class)]
class UserGameKey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?string $gameKey = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'userGameKeys')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userGameKeys')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?Game $game = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameKey(): ?string
    {
        return $this->gameKey;
    }

    public function setGameKey(string $gameKey): static
    {
        $this->gameKey = $gameKey;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }
}
