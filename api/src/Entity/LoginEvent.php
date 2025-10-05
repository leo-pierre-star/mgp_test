<?php

namespace App\Entity;

use App\Repository\LoginEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoginEventRepository::class)]
#[ORM\Index(columns: ['occurred_at'], name: 'login_event_occurred_idx')]
class LoginEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(name: 'occurred_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $occurredAt = null;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getOccurredAt(): ?\DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
