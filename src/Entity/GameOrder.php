<?php

namespace App\Entity;

use App\Repository\GameOrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GameOrderRepository::class)]
class GameOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?float $totalPrice = null;

    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'gameOrders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?Game $game = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'gameOrders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?Order $order = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

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

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }
}
