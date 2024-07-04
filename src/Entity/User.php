<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?string $lastname = null;

    #[ORM\Column(length: 50)]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?string $nickname = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?string $picture = null;

    #[ORM\Column(length: 100)]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 100)]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?Theme $chooseTheme = null;
    
    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(length: 100)]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private ?array $roles = [];

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'users')]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private Collection $selectedCategory;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'users')]
    #[Groups([
        'user_browse',
        'user_show'
    ])]
    private Collection $preferedTag;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user')]
    private Collection $purchasedOrder;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\ManyToMany(targetEntity: Game::class, inversedBy: 'users')]
    private Collection $userGetGame;

    /**
     * @var Collection<int, UserGameKey>
     */
    #[ORM\OneToMany(targetEntity: UserGameKey::class, mappedBy: 'user')]
    private Collection $userGameKeys;

    public function __construct()
    {
        $this->selectedCategory = new ArrayCollection();
        $this->preferedTag = new ArrayCollection();
        $this->purchasedOrder = new ArrayCollection();
        $this->userGetGame = new ArrayCollection();
        $this->userGameKeys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getChooseTheme(): ?Theme
    {
        return $this->chooseTheme;
    }

    public function setChooseTheme(?Theme $chooseTheme): static
    {
        $this->chooseTheme = $chooseTheme;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getSelectedCategory(): Collection
    {
        return $this->selectedCategory;
    }

    public function addSelectedCategory(Category $selectedCategory): static
    {
        if (!$this->selectedCategory->contains($selectedCategory)) {
            $this->selectedCategory->add($selectedCategory);
        }

        return $this;
    }

    public function removeSelectedCategory(Category $selectedCategory): static
    {
        $this->selectedCategory->removeElement($selectedCategory);

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getPreferedTag(): Collection
    {
        return $this->preferedTag;
    }

    public function addPreferedTag(Tag $preferedTag): static
    {
        if (!$this->preferedTag->contains($preferedTag)) {
            $this->preferedTag->add($preferedTag);
        }

        return $this;
    }

    public function removePreferedTag(Tag $preferedTag): static
    {
        $this->preferedTag->removeElement($preferedTag);

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getPurchasedOrder(): Collection
    {
        return $this->purchasedOrder;
    }

    public function addPurchasedOrder(Order $purchasedOrder): static
    {
        if (!$this->purchasedOrder->contains($purchasedOrder)) {
            $this->purchasedOrder->add($purchasedOrder);
            $purchasedOrder->setUser($this);
        }

        return $this;
    }

    public function removePurchasedOrder(Order $purchasedOrder): static
    {
        if ($this->purchasedOrder->removeElement($purchasedOrder)) {
            // set the owning side to null (unless already changed)
            if ($purchasedOrder->getUser() === $this) {
                $purchasedOrder->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getUserGetGame(): Collection
    {
        return $this->userGetGame;
    }

    public function addUserGetGame(Game $userGetGame): static
    {
        if (!$this->userGetGame->contains($userGetGame)) {
            $this->userGetGame->add($userGetGame);
        }

        return $this;
    }

    public function removeUserGetGame(Game $userGetGame): static
    {
        $this->userGetGame->removeElement($userGetGame);

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
            $userGameKey->setUser($this);
        }

        return $this;
    }

    public function removeUserGameKey(UserGameKey $userGameKey): static
    {
        if ($this->userGameKeys->removeElement($userGameKey)) {
            // set the owning side to null (unless already changed)
            if ($userGameKey->getUser() === $this) {
                $userGameKey->setUser(null);
            }
        }

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
