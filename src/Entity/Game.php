<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'game_browse',
        'game_show',
        'user_browse',
        'user_show'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups([
        'game_browse',
        'game_show',
        'user_browse',
        'user_show'
    ])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups([
        'game_browse',
        'game_show'
    ])]
    private ?\DateTimeImmutable $releaseDate = null;

    #[ORM\Column]
    #[Groups([
        'game_browse',
        'game_show'
    ])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'game_browse',
        'game_show'
    ])]
    private ?string $picture = null;

    #[ORM\Column]
    #[Groups([
        'game_browse',
        'game_show'
    ])]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'game_browse',
        'game_show'
    ])]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Groups([
        'game_browse',
        'game_show'
    ])]
    private ?string $editor = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'games')]
    #[Groups([
        'game_browse',
        'game_show'
    ])]
    private Collection $hasTag;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'games')]
    #[Groups([
        'game_browse',
        'game_show',
    ])]
    private Collection $hasCategory;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'userGetGame')]
    private Collection $users;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\ManyToMany(targetEntity: Order::class, inversedBy: 'games')]
    private Collection $gameHasOrder;

    /**
     * @var Collection<int, UserGameKey>
     */
    #[ORM\OneToMany(targetEntity: UserGameKey::class, mappedBy: 'game')]
    private Collection $userGameKeys;

    /**
     * @var Collection<int, ValidateOrder>
     */
    #[ORM\ManyToMany(targetEntity: ValidateOrder::class, mappedBy: 'game')]
    private Collection $validateOrders;

    

    

    public function __construct()
    {
        $this->hasTag = new ArrayCollection();
        $this->hasCategory = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->gameHasOrder = new ArrayCollection();
        
        // Initialisation de createdAt et releaseDate avec la date et l'heure actuelles
        $this->createdAt = new \DateTimeImmutable();
        $this->releaseDate = new \DateTimeImmutable();
        $this->userGameKeys = new ArrayCollection();
        $this->validateOrders = new ArrayCollection();
        
    }


   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeImmutable
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeImmutable $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEditor(): ?string
    {
        return $this->editor;
    }

    public function setEditor(string $editor): static
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getHasTag(): Collection
    {
        return $this->hasTag;
    }

    public function addHasTag(Tag $hasTag): static
    {
        if (!$this->hasTag->contains($hasTag)) {
            $this->hasTag->add($hasTag);
        }

        return $this;
    }

    public function removeHasTag(Tag $hasTag): static
    {
        $this->hasTag->removeElement($hasTag);

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getHasCategory(): Collection
    {
        return $this->hasCategory;
    }

    public function addHasCategory(Category $hasCategory): static
    {
        if (!$this->hasCategory->contains($hasCategory)) {
            $this->hasCategory->add($hasCategory);
        }

        return $this;
    }

    public function removeHasCategory(Category $hasCategory): static
    {
        $this->hasCategory->removeElement($hasCategory);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addUserGetGame($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeUserGetGame($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getGameHasOrder(): Collection
    {
        return $this->gameHasOrder;
    }

    public function addGameHasOrder(Order $gameHasOrder): static
    {
        if (!$this->gameHasOrder->contains($gameHasOrder)) {
            $this->gameHasOrder->add($gameHasOrder);
        }

        return $this;
    }

    public function removeGameHasOrder(Order $gameHasOrder): static
    {
        $this->gameHasOrder->removeElement($gameHasOrder);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, UserGameKey>
     */
    public function getUserGameKeys(): Collection
    {
        return $this->userGameKeys;
    }

    public function addUserGameKey(UserGameKey $userGameKey): static
    {
        if (!$this->userGameKeys->contains($userGameKey)) {
            $this->userGameKeys->add($userGameKey);
            $userGameKey->setGame($this);
        }

        return $this;
    }

    public function removeUserGameKey(UserGameKey $userGameKey): static
    {
        if ($this->userGameKeys->removeElement($userGameKey)) {
            // set the owning side to null (unless already changed)
            if ($userGameKey->getGame() === $this) {
                $userGameKey->setGame(null);
            }
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
            $validateOrder->addGame($this);
        }

        return $this;
    }

    public function removeValidateOrder(ValidateOrder $validateOrder): static
    {
        if ($this->validateOrders->removeElement($validateOrder)) {
            $validateOrder->removeGame($this);
        }

        return $this;
    }

    
}
