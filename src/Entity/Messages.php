<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]
#[Groups(['messages'])]
class Messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column]
    private ?bool $isRead = null;

    #[ORM\ManyToOne(inversedBy: 'sent')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'received')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recipient = null;

    #[ORM\Column(type: 'boolean')]
    private bool $deletedForRecipient = false;

    #[ORM\Column(type: 'boolean')]
    private bool $deletedForSender = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setRead(bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    
    public function isDeletedForRecipient(): bool
    {
        return $this->deletedForRecipient;
    }
    /**
     * Get the value of deletedForRecipient
     */ 
    public function getDeletedForRecipient()
    {
        return $this->deletedForRecipient;
    }
    
    /**
     * Set the value of deletedForRecipient
     *
     * @return  self
     */ 
    public function setDeletedForRecipient($deletedForRecipient)
    {
        $this->deletedForRecipient = $deletedForRecipient;
        
        return $this;
    }
    
    public function isDeletedForSender(): bool
    {
        return $this->deletedForSender;
    }
    /**
     * Get the value of deletedForSender
     */ 
    public function getDeletedForSender()
    {
        return $this->deletedForSender;
    }
    
    /**
     * Set the value of deletedForSender
     *
     * @return  self
     */ 
    public function setDeletedForSender($deletedForSender)
    {
        $this->deletedForSender = $deletedForSender;

        return $this;
    }
}
