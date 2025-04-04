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

    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $affiliationLocation = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $specialization = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $researchTopic = null;

    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'subscribedUsers')]
    #[ORM\JoinTable(name: 'Subscription')]
    private Collection $subscribedPosts;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserLike::class)]
    private Collection $user_likes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserQuestions::class, cascade: ['persist', 'remove'])]
    private Collection $userQuestions;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: Post::class)]
    private Collection $posts;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: Comment::class)]
    private Collection $comments;

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

    #[ORM\Column(type: 'integer')]
    private ?int $userType = 0;

    public function __construct()
    {
        $this->subscribedPosts = new ArrayCollection();
        $this->user_likes = new ArrayCollection();
        $this->userQuestions = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getAffiliationLocation(): ?string
    {
        return $this->affiliationLocation;
    }

    public function setAffiliationLocation(?string $affiliationLocation): static
    {
        $this->affiliationLocation = $affiliationLocation;

        return $this;
    }

    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    public function setSpecialization(?string $specialization): static
    {
        $this->specialization = $specialization;

        return $this;
    }

    public function getResearchTopic(): ?string
    {
        return $this->researchTopic;
    }

    public function setResearchTopic(?string $researchTopic): static
    {
        $this->researchTopic = $researchTopic;

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

    public function getUserQuestions(): Collection
    {
        return $this->userQuestions;
    }

    public function addUserQuestion(UserQuestions $userQuestion): static
    {
        if (!$this->userQuestions->contains($userQuestion)) {
            $this->userQuestions->add($userQuestion);
            $userQuestion->setUser($this);
        }

        return $this;
    }

    public function removeUserQuestion(UserQuestions $userQuestion): static
    {
        if ($this->userQuestions->removeElement($userQuestion)) {
            // set the owning side to null (unless already changed)
            if ($userQuestion->getUser() === $this) {
                $userQuestion->setUser(null);
            }
        }

        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUserId($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUserId() === $this) {
                $post->setUserId(null);
            }
        }

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUserId($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUserId() === $this) {
                $comment->setUserId(null);
            }
        }

        return $this;
    }

    public function getUserType(): ?int
    {
        return $this->userType;
    }

    public function setUserType(int $userType): static
    {
        $this->userType = $userType;

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
