<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_ID', fields: ['id'])]
#[UniqueEntity(fields: ['id'], message: 'There is already an account with this id')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $whoami = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $myhobbies = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $whatimdoing = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $mygoals = null;

    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'subscribedUsers')]
    #[ORM\JoinTable(name: 'Subscription')]
    private Collection $subscribedPosts;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserLike::class)]
    private Collection $user_likes;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    public function __construct()
    {
        $this->subscribedPosts = new ArrayCollection();
        $this->user_likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->id;
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
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getWhoami(): ?string
    {
        return $this->whoami;
    }

    public function setWhoami(?string $whoami): static
    {
        $this->whoami = $whoami;

        return $this;
    }

    public function getMyhobbies(): ?string
    {
        return $this->myhobbies;
    }

    public function setMyhobbies(?string $myhobbies): static
    {
        $this->myhobbies = $myhobbies;

        return $this;
    }

    public function getWhatimdoing(): ?string
    {
        return $this->whatimdoing;
    }

    public function setWhatimdoing(?string $whatimdoing): static
    {
        $this->whatimdoing = $whatimdoing;

        return $this;
    }

    public function getMygoals(): ?string
    {
        return $this->mygoals;
    }

    public function setMygoals(?string $mygoals): static
    {
        $this->mygoals = $mygoals;

        return $this;
    }

    public function getSubscribedPosts(): Collection
    {
        return $this->subscribedPosts;
    }

    public function addSubscribedPost(Post $Post): static
    {
        if (!$this->subscribedPosts->contains($Post)) {
            $this->subscribedPosts->add($Post);
        }

        return $this;
    }

    public function removeSubscribedPost(Post $Post): static
    {
        $this->subscribedPosts->removeElement($Post);

        return $this;
    }

    public function getUserLikes(): Collection
    {
        return $this->user_likes;
    }

    public function addUserLike(UserLike $user_like): static
    {
        if (!$this->user_likes->contains($user_like)) {
            $this->user_likes->add($user_like);
            $user_like->setUser($this);
        }

        return $this;
    }

    public function removeUserLike(UserLike $user_like): static
    {
        if ($this->user_likes->removeElement($user_like)) {
            // set the owning side to null (unless already changed)
            if ($user_like->getUser() === $this) {
                $user_like->setUser(null);
            }
        }

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
