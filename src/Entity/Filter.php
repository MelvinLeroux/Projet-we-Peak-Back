<?php

namespace App\Entity;
use App\Repository\FilterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FilterRepository::class)]
#[Groups(["filter"])]
#[ORM\HasLifecycleCallbacks]
class Filter
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['activity.list', 'activity.show'])] 
    #[Assert\NotBlank(message: 'La catégorie (slug) ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9][a-zA-Z0-9\-_+%]*$/',
        message: 'La catégorie (slug) doit commencer par une lettre ou un chiffre et ne peut pas contenir d\'espaces'
    )]
    private ?string $category = null;
    
    #[ORM\Column(length: 50)]
    #[Groups(['activity.list', 'activity.show'])]
    #[Assert\NotBlank(message: 'La catégorie a afficher ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/^[A-Z0-9]/',
        message: 'La catégorie a afficher doit commencer par une majuscule ou un chiffre'
    )]
    private ?string $categoryLabel = null;
    
    #[ORM\Column(length: 255)]
    #[Groups(['activity.show', 'activity.list'])]
    #[Assert\NotBlank(message: 'La valeur (slug) ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9][a-zA-Z0-9\-_+%]*$/',
        message: 'La valeur (slug) doit commencer par une lettre ou un chiffre et être séparé par des tirets'
        )]
        private ?string $value = null;
        
    #[ORM\Column(length: 50)]
    #[Groups(['activity.list', 'activity.show'])]
    #[Assert\NotBlank(message: 'La valeur a afficher ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/^[A-Z0-9]/',
        message: 'La valeur à afficher doit commencer par une majuscule ou un chiffre'
    )]
    private ?string $valueLabel = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getValueLabel(): ?string
    {
        return $this->valueLabel;
    }

    public function setValueLabel(string $valueLabel): static
    {
        $this->valueLabel = $valueLabel;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCategoryLabel(): ?string
    {
        return $this->categoryLabel;
    }

    public function setCategoryLabel(string $categoryLabel): static
    {
        $this->categoryLabel = $categoryLabel;

        return $this;
    }
}
