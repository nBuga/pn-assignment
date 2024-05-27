<?php

namespace App\Entity;

use App\Repository\UserPrizeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserPrizeRepository::class)]
class UserPrize
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userPrizes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userPrizes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prize $prize = null;

    #[ORM\Column]
    private ?\DateTime $receivedPrizeAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User|UserInterface|null $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPrize(): ?Prize
    {
        return $this->prize;
    }

    public function setPrize(?Prize $prize): static
    {
        $this->prize = $prize;

        return $this;
    }

    public function getReceivedPrizeAt(): ?\DateTime
    {
        return $this->receivedPrizeAt;
    }

    public function setReceivedPrizeAt(\DateTime $receivedPrizeAt): static
    {
        $this->receivedPrizeAt = $receivedPrizeAt;

        return $this;
    }
}
