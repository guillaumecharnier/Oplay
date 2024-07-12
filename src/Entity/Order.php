<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'float')]
    private ?float $total = null;

    #[ORM\Column(length: 25)]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, GameOrder>
     */
    #[ORM\OneToMany(targetEntity: GameOrder::class, mappedBy: 'order', cascade: ['persist', 'remove'])]
    private Collection $gameOrders;

    public function __construct()
    {
        $this->gameOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
    * @return Collection<int, GameOrder>
    */
   public function getGameOrders(): Collection
   {
       return $this->gameOrders;
   }

   public function addGameOrder(GameOrder $gameOrder): static
   {
       if (!$this->gameOrders->contains($gameOrder)) {
           $this->gameOrders[] = $gameOrder;
           $gameOrder->setOrder($this);
       }

       return $this;
   }

   public function removeGameOrder(GameOrder $gameOrder): static
   {
       $this->gameOrders->removeElement($gameOrder);
       // set the owning side to null (unless already changed)
       if ($gameOrder->getOrder() === $this) {
           $gameOrder->setOrder(null);
       }

       return $this;
   }
}
