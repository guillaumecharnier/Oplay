<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
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
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 50)]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?float $total = null;

    #[ORM\ManyToOne(inversedBy: 'purchasedOrder')]
    private ?User $user = null;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'gameHasOrder')]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private Collection $games;

    /**
     * @var Collection<int, ValidateOrder>
     */
    #[ORM\ManyToMany(targetEntity: ValidateOrder::class, mappedBy: 'orders')]
    private Collection $validateOrders;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->validateOrders = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

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

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->addGameHasOrder($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            $game->removeGameHasOrder($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ValidateOrder>
     */
    public function getValidateOrders(): Collection
    {
        return $this->validateOrders;
    }

    public function addValidateOrder(ValidateOrder $validateOrder): static
    {
        if (!$this->validateOrders->contains($validateOrder)) {
            $this->validateOrders->add($validateOrder);
            $validateOrder->addOrder($this);
        }

        return $this;
    }

    public function removeValidateOrder(ValidateOrder $validateOrder): static
    {
        if ($this->validateOrders->removeElement($validateOrder)) {
            $validateOrder->removeOrder($this);
        }

        return $this;
    }


}
