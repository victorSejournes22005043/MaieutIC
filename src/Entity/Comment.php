<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000)]
    private ?string $body = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $PostId = null;

    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: UserLike::class)]
    private Collection $user_likes;

    public function __construct()
    {
        $this->user_likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getPostId(): ?Post
    {
        return $this->PostId;
    }

    public function setPostId(Post $PostId): static
    {
        $this->PostId = $PostId;

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
            $user_like->setComment($this);
        }

        return $this;
    }

    public function removeUserLike(UserLike $user_like): static
    {
        if ($this->user_likes->removeElement($user_like)) {
            // set the owning side to null (unless already changed)
            if ($user_like->getComment() === $this) {
                $user_like->setComment(null);
            }
        }

        return $this;
    }
}
