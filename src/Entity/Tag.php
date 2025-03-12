<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Post;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'tags')]
    private Collection $Posts;

    public function __construct()
    {
        $this->Posts = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->Posts;
    }

    public function addPost(Post $Post): static
    {
        if (!$this->Posts->contains($Post)) {
            $this->Posts->add($Post);
            $Post->addTag($this);
        }

        return $this;
    }

    public function removePost(Post $Post): static
    {
        if ($this->Posts->removeElement($Post)) {
            $Post->removeTag($this);
        }

        return $this;
    }
}