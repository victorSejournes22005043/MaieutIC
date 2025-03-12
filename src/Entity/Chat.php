<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $user = null;

    #[ORM\Column(type: 'text')]
    private ?string $body = null;

    #[ORM\ManyToOne(targetEntity: ChatBox::class, inversedBy: 'chats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ChatBox $chatBox = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): static
    {
        $this->user = $user;

        return $this;
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

    public function getChatBox(): ?ChatBox
    {
        return $this->chatBox;
    }

    public function setChatBox(?ChatBox $chatBox): static
    {
        $this->chatBox = $chatBox;

        return $this;
    }
}
