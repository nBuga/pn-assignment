<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
#[ORM\Table(name: 'partner')]
class Partner implements TranslatableInterface
{
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    /**
     * @var Collection<int, Prize>
     */
    #[ORM\OneToMany(mappedBy: 'partnerCode', targetEntity: Prize::class, orphanRemoval: true)]
    private Collection $prizes;

    public function __construct()
    {
        $this->prizes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

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

    /**
     * @return Collection<int, Prize>
     */
    public function getPrizes(): Collection
    {
        return $this->prizes;
    }

    public function addPrize(Prize $prize): static
    {
        if (!$this->prizes->contains($prize)) {
            $this->prizes->add($prize);
            $prize->setPartnerCode($this);
        }

        return $this;
    }

    public function removePrize(Prize $prize): static
    {
        if ($this->prizes->removeElement($prize)) {
            // set the owning side to null (unless already changed)
            if ($prize->getPartnerCode() === $this) {
                $prize->setPartnerCode(null);
            }
        }

        return $this;
    }
}
