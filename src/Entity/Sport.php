<?php

namespace App\Entity;

use App\Repository\SportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: SportRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Sport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['activity.show','activity.create','sport.list','sport.show', 'sport.create', 'user.show'])]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 100)]
    #[Groups(['activity.show','sport.list','sport.show','sport.create','activity.list', 'user.show', 'participation.list', 'participation.show'])]
    private ?string $name = null;
    
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups(['activity.list','activity.show','activity.create', 'user.show', 'participation.list', 'participation.show','sport.list','sport.show'])]
    private ?string $label = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    #[Groups(['sport.show','sport.create'])]
    private ?string $description = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['sport.show','sport.create'])]
    private ?string $slug = null;

    #[Assert\DateTime]
    #[ORM\Column]
    #[Groups(['sport.create'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: "App\Entity\Difficulty", inversedBy: "sports", cascade: ["persist"])]
    #[ORM\JoinTable(name: "sport_difficulty")]
    #[Groups(['activity.list','activity.show','activity.create', 'sport.list','sport.show'])]
    private Collection $difficulties;

    #[ORM\OneToMany(targetEntity: "App\Entity\Difficulty", mappedBy: "sport", cascade: ["persist"])]
    private Collection $newDifficulties;

    #[ORM\ManyToMany(targetEntity: "App\Entity\Activity", mappedBy: "sports")]
    private Collection $activities;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'sports')]
    private Collection $users;




    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->difficulties = new ArrayCollection();
        $this->newDifficulties = new ArrayCollection();
        $this->users = new ArrayCollection();

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

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

    public function getDifficulties(): Collection
    {
        return $this->difficulties;
    }

    public function addDifficulty(Difficulty $difficulty): self
    {
        if (!$this->difficulties->contains($difficulty)) {
            $this->difficulties[] = $difficulty;
            $difficulty->addSport($this); // Assure la relation bidirectionnelle
        }

        return $this;
    }

    public function removeDifficulty(Difficulty $difficulty): self
    {
        $this->difficulties->removeElement($difficulty);
        $difficulty->removeSport($this); // Assure la relation bidirectionnelle

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
        }
        return $this;
    }

    public function removeActivity(Activity $activity): static
    {
        $this->activities->removeElement($activity);
        return $this;
    }


    public function __toString()
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



    // Méthodes pour les nouvelles difficultés

    public function getNewDifficulties(): Collection
    {
        return $this->newDifficulties;
    }

    public function addNewDifficulty(Difficulty $difficulty): self
    {
        if (!$this->newDifficulties->contains($difficulty)) {
            $this->newDifficulties[] = $difficulty;
            $difficulty->addSport($this); // Utilisation de la méthode addSport pour établir la relation
        }
    
        return $this;
    }

    public function removeNewDifficulty(Difficulty $difficulty): self
    {
        $this->newDifficulties->removeElement($difficulty);
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
            $user->addSport($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeSport($this);
        }

        return $this;
    }

}
