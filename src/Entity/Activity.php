<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['activity.list','activity.show','activity.create','sport.show','user.show','participation.list'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['activity.list','activity.show','sport.show','user.show','activity.create','participation.list'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['activity.show','activity.create'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['activity.list','activity.show','activity.create', 'participation.list', 'user.show'])]
    private ?\DateTime $date = null;

    #[ORM\Column]
    #[Groups(['activity.list','activity.show','activity.create'])]
    #[Assert\Range(max : 30, notInRangeMessage:"IL ne peut y avoir que {{max}} participants.")]
    private ?int $groupSize = null;
    
   

    #[Vich\UploadableField(mapping: 'activities', fileNameProperty: 'thumbnail')]
    #[Assert\Image()]
    private ?File $thumbnailFile = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user.show', 'activity.show', 'activity.list'])]
    private ?string $city = null;

    #[ORM\Column]
    #[Groups(['activity.list','activity.show','activity.create'])]
    private ?float $lat = null;

    #[ORM\Column]
    #[Groups(['activity.list','activity.show','activity.create'])]
    private ?float $lng = null;

    #[ORM\Column]
    #[Assert\DateTime]
    #[Groups(['activity.create'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Participation::class, mappedBy: 'activity', cascade: ['remove'])]
    #[Groups(['activity.show', 'activity.create', 'activity.list'])]
    private Collection $participations;

    #[ORM\ManyToOne(inversedBy: 'activitiesCreated', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['activity.create', 'activity.show',])]
    private ?User $createdBy = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Blank]
    #[Groups(['activity.show','sport.show','user.show, activity.create'])]
    private ?string $slug = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Difficulty", inversedBy: "activities")]
    #[ORM\JoinColumn(name: "difficulty_id", referencedColumnName: "id")]
    #[Groups(['activity.list','activity.show','activity.create'])]
    private ?Difficulty $difficulty;

    #[ORM\ManyToMany(targetEntity: "App\Entity\Sport", inversedBy: "activities")]
    #[ORM\JoinTable(name: "sport_activity")]
    #[Groups(['activity.list','activity.show','activity.create','participation.list', 'user.show'])]
    private Collection $sports;

    #[ORM\OneToMany(targetEntity: Pictures::class, mappedBy: 'activity',cascade: ['remove'])]
    #[Groups(['activity.create','activity.show'])]
    private Collection $pictures;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'activity', cascade: ['remove'])]
    private Collection $comments;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['activity.show','activity.create', 'user.show', 'activity.list'])]
    private ?string $thumbnail = null;

    public function __construct()
    {
        $this->sports = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->participations = new ArrayCollection();
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

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getGroupSize(): ?int
    {
        return $this->groupSize;
    }

    public function setGroupSize(int $groupSize): static
    {
        $this->groupSize = $groupSize;

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

    /**
     * @return Collection<int, Participation>
     */
    
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setActivity($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getActivity() === $this) {
                $participation->setActivity(null);
            }
        }

        return $this;
    }


    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

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


    public function getThumbnailFile()
    {
        return $this->thumbnailFile;
    }

    public function setThumbnailFile($thumbnailFile): static
    {
        $this->thumbnailFile = $thumbnailFile;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getDifficulty(): ?Difficulty
    {
        return $this->difficulty;
    }

    public function setDifficulty(?Difficulty $difficulty): static
    {
        $this->difficulty = $difficulty;
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
            $sport->addActivity($this);
            
        }
        return $this;
    }

    public function removeSport(Sport $sport): static
    {
        $this->sports->removeElement($sport);
        $sport->removeActivity($this);
        return $this;
    }

    public function __toString()
    {
        return $this->name;
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

    /**
     * @return Collection<int, Pictures>
     */
    public function getPictures(): Collection
        
    {
        return $this->pictures;
    }

    public function addPicture(Pictures $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setActivity($this);
        }

        return $this;
    }

    public function removePicture(Pictures $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getActivity() === $this) {
                $picture->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setActivity($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getActivity() === $this) {
                $comment->setActivity(null);
            }
        }

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

}
