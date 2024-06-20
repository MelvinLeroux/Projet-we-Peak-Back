<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[Vich\Uploadable()]
#[ORM\HasLifecycleCallbacks]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['activity.show', 'activity.list','user.list','user.show','user.create','participation.list'])]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['activity.show', 'activity.list','user.list','user.show','user.create','participation.list'])]
    private ?string $pseudo = null;

    #[Assert\Length(
        min:8, max: 20,
        minMessage: 'Votre mot de passe doit faire au minimum {{ limit }} caractères',
        maxMessage: 'Votre mot de passe doit faire au maximum {{ limit }} caractères')]
    #[Assert\Regex(pattern: '/(?=.*\d)(?=.*[A-Z])(?=.*\W)/',match : false, message :"Le mot de passe doit contenir au moins une majuscule, un chiffre et un caractère spécial")]
    #[ORM\Column(length: 100)]
    private ?string $password = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['user.show','user.create'])]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 40,
        minMessage: 'Votre prénom doit comporter {{ limit }} charactères minimum',
        maxMessage: 'Votre prénom doit comporter {{ limit }} charactères maximum',
    )]
    #[Assert\Regex(pattern: "/\d/", match : false, message : "Le prénom ne doit pas contenir de chiffre")]
    #[ORM\Column(length: 50)]
    #[Groups(['user.show','user.create'])]
    private ?string $firstname = null;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 40,
        minMessage: 'Votre nom doit comporter {{ limit }} charactères minimum',
        maxMessage: 'Votre nom doit comporter {{ limit }} charactères maximum',
    )]
    #[Assert\Regex(pattern: "/\d/", match : false, message : "Le nom ne doit pas contenir de chiffre")]
    #[ORM\Column(length: 50)]
    #[Groups(['user.show','user.create'])]
    private ?string $lastname = null;

    #[Assert\NotBlank]
    #[Assert\Type("\DateTimeInterface")]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['user.show','user.create'])]
    private ?\DateTimeInterface $birthdate = null;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 1,
        max: 40,
        minMessage: 'La ville doit comporter {{ limit }} charactères minimum',
        maxMessage: 'La ville doit comporter {{ limit }} charactères maximum',
    )]
    #[ORM\Column(length: 50)]
    #[Groups(['user.show','user.create'])]
    private ?string $city = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user.create'])]
    private array $roles = [];

    #[Assert\DateTime]
    #[ORM\Column]
    #[Groups(['user.show'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[Assert\DateTime]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Participation::class, mappedBy: 'user',cascade: ['remove',])]
    #[Groups(['user.show'])]
    private Collection $participations;

    
    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'createdBy',cascade: ['remove'])]
    #[Groups(['user.show'])]
    private Collection $activitiesCreated;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['user.show'])]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user.show', 'user.create','participation.list','participation.show','activity.show'])]
    private ?string $thumbnail = null;

    #[Vich\UploadableField(mapping: 'users', fileNameProperty: 'thumbnail')]
    #[Assert\Image()]
    private ?File $thumbnailFile = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user.show', 'user.create'])]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Pictures::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $pictures;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $comments;

    /**
     * @var Collection<int, Messages>
     */
    #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'sender', orphanRemoval: true)]
    private Collection $sent;

    /**
     * @var Collection<int, Messages>
     */
    #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'recipient', orphanRemoval: true)]
    private Collection $received;

    #[ORM\Column]
    private ?bool $age = null;

    /**
     * @var Collection<int, Sport>
     */
    #[ORM\ManyToMany(targetEntity: Sport::class, inversedBy: 'users')]
    #[Groups(['user.show'])]
    private Collection $sports;


    
    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->activitiesCreated = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->sent = new ArrayCollection();
        $this->received = new ArrayCollection();
        $this->sports = new ArrayCollection();

    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
    
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

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
            $participation->setUser($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getUser() === $this) {
                $participation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivitiesCreated(): Collection
    {
        return $this->activitiesCreated;
    }

    public function addActivitiesCreated(Activity $activitiesCreated): static
    {
        if (!$this->activitiesCreated->contains($activitiesCreated)) {
            $this->activitiesCreated->add($activitiesCreated);
            $activitiesCreated->setCreatedBy($this);
        }

        return $this;
    }

    public function removeActivitiesCreated(Activity $activitiesCreated): static
    {
        if ($this->activitiesCreated->removeElement($activitiesCreated)) {
            // set the owning side to null (unless already changed)
            if ($activitiesCreated->getCreatedBy() === $this) {
                $activitiesCreated->setCreatedBy(null);
            }
        }

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

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): static
    {
        if ($thumbnail === null || $thumbnail === '') {
            // Si c'est le cas, définissez un fichier par défaut
            $this->thumbnail = '/public/image/avatar.svg';
        } else {
            // Sinon, utilisez la valeur fournie
            $this->thumbnail = $thumbnail;
        }

        return $this;
    }


    /**
     * Get the value of thumbnailFile
     */ 
    public function getThumbnailFile()
    {
        return $this->thumbnailFile;
    }

    /**
     * Set the value of thumbnailFile
     *
     * @return  self
     */ 
    public function setThumbnailFile($thumbnailFile) : static
    {
        $this->thumbnailFile = $thumbnailFile;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified)
    {
        $this->isVerified = $isVerified;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getActivitiesCreatedCount(): int
    {
        return $this->getActivitiesCreated()->count();
    }


    public function __toString()
    {
        return $this->getPseudo();
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
            $picture->setUser($this);
        }

        return $this;
    }

    public function removePicture(Pictures $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getUser() === $this) {
                $picture->setUser(null);
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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getSent(): Collection
    {
        return $this->sent;
    }

    public function addSent(Messages $sent): static
    {
        if (!$this->sent->contains($sent)) {
            $this->sent->add($sent);
            $sent->setSender($this);
        }

        return $this;
    }

    public function removeSent(Messages $sent): static
    {
        if ($this->sent->removeElement($sent)) {
            // set the owning side to null (unless already changed)
            if ($sent->getSender() === $this) {
                $sent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getReceived(): Collection
    {
        return $this->received;
    }

    public function addReceived(Messages $received): static
    {
        if (!$this->received->contains($received)) {
            $this->received->add($received);
            $received->setRecipient($this);
        }

        return $this;
    }

    public function removeReceived(Messages $received): static
    {
        if ($this->received->removeElement($received)) {
            // set the owning side to null (unless already changed)
            if ($received->getRecipient() === $this) {
                $received->setRecipient(null);
            }
        }

        return $this;
    }

    public function isAge(): ?bool
    {
        return $this->age;
    }

    public function setAge(bool $age): static
    {
        $this->age = $age;

        return $this;
    }

    /**
     * @return Collection<int, Sport>
     */
    public function getSports(): Collection
    {
        return $this->sports;
    }

    public function addSport(Sport $sport): static
    {
        if (!$this->sports->contains($sport)) {
            $this->sports->add($sport);
        }

        return $this;
    }

    public function removeSport(Sport $sport): static
    {
        $this->sports->removeElement($sport);

        return $this;
    }

}