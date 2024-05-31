<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PrizeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

#[ORM\Entity(repositoryClass: PrizeRepository::class)]
#[ORM\Table(name: 'prize')]
class Prize implements TranslatableInterface
{
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prizes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Partner $partner = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column]
    private ?int $stock = 0;

    /**
     * @var Collection<int, UserPrize>
     */
    #[ORM\OneToMany(mappedBy: 'prize', targetEntity: UserPrize::class)]
    private Collection $userPrizes;

    public function __construct()
    {
        $this->userPrizes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): static
    {
        $this->partner = $partner;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, UserPrize>
     */
    public function getUserPrizes(): Collection
    {
        return $this->userPrizes;
    }

    public function addUserPrize(UserPrize $userPrize): static
    {
        if (!$this->userPrizes->contains($userPrize)) {
            $this->userPrizes->add($userPrize);
            $userPrize->setPrize($this);
        }

        return $this;
    }

    public function removeUserPrize(UserPrize $userPrize): static
    {
        if ($this->userPrizes->removeElement($userPrize)) {
            if ($userPrize->getPrize() === $this) {
                $userPrize->setPrize(null);
            }
        }

        return $this;
    }

    public function getNameTranslated($locale = 'en'): string
    {
        return $this->translate($locale)->getName();
    }
}
