<?php

namespace App\Entity;
use App\Repository\DifficultyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DifficultyRepository::class)]
#[Groups(["difficulty"])]
#[ORM\HasLifecycleCallbacks]
class Difficulty
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['sport.list','sport.show'])]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['activity.list','activity.show','activity.create','sport.list','sport.show'])]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9][a-zA-Z0-9\-_+%]*$/',
        message: 'La valeur (slug) doit commencer par une lettre ou un chiffre et être séparé par des tirets ou des majuscules'
        )]
    private ?string $value = null;
    
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['activity.list','activity.show','activity.create', 'sport.list','sport.show'])]
    #[Assert\Regex(
        pattern: '/^[A-Z0-9]/',
        message: 'La valeur à afficher doit commencer par une majuscule ou un chiffre'
    )]
    private ?string $label = null;

    #[ORM\Column]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $updatedAt = null;


    #[ORM\ManyToMany(targetEntity: "App\Entity\Sport", mappedBy: "difficulties")]
    private Collection $sports;

    #[ORM\OneToMany(targetEntity: "App\Entity\Activity", mappedBy: "difficulty")]
    private Collection $activities;

    public function __construct()
    {
        $this->sports = new ArrayCollection();
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getSports(): Collection
    {
        return $this->sports;
    }


    public function addSport(Sport $sport): static
    {
        if (!$this->sports->contains($sport)) {
            $this->sports[] = $sport;
            $sport->addDifficulty($this); // Assure la relation bidirectionnelle
        }
        return $this;
    }

public function removeSport(Sport $sport): static
    {
        $this->sports->removeElement($sport);
        $sport->removeDifficulty($this); // Assure la relation bidirectionnelle

        return $this;
    }

    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setDifficulty($this);
        }
        return $this;
    }

    public function removeActivity(Activity $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getDifficulty() === $this) {
                $activity->setDifficulty(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->label;
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

}
