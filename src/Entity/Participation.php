<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user.show','activity.show', 'activity.list','participation.list','participation.create', 'participation.show'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    #[Groups(['user.show','activity.show','activity.create','participation.list','participation.create', 'participation.show'])]
    private ?int $status = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[Groups(['activity.show', 'activity.list', 'activity.create','participation.list','participation.create', 'participation.show'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[Groups(['user.show','participation.list','participation.create', 'participation.show'])]
    private ?Activity $activity = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): static
    {
        $this->activity = $activity;

        return $this;
    }

    #[Assert\Callback(groups: ['participation.create'])]
    public function validateUniqueParticipation(ExecutionContextInterface $context, $payload)
    {
        // Vérifier si l'utilisateur participe déjà à cette activité
        if ($this->getUser() && $this->getActivity()) {
            $existingParticipation = $this->getActivity()->getParticipations()->filter(function ($participation) {
                return $participation->getUser() === $this->getUser();
            });

            if ($existingParticipation->count() > 0) {
                $context->buildViolation('User is already participating in this activity.')
                    ->atPath('user')
                    ->addViolation();
            }
        }
    }
    

    public function __toString(): string
    {
        return $this->getActivity()->getName();
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
