<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = "expr('/room/' ~ object.getUid())",
 *      exclusion = @Hateoas\Exclusion(groups="room")
 * )
 */
#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[ApiResource]
#[ORM\HasLifecycleCallbacks]
class Room
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(["room"])]
    private ?Uuid $uid = null;

    #[ORM\Column(length: 128)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 128,
        maxMessage: 'Le nom de la salle ne doit pas dépasser les 128 caractères',
    )]
    #[Assert\NotBlank(message: "Le nom de la salle ('name')  est obligatoire")]
    #[Groups(["room"])]
    private ?string $name = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\GreaterThan(
        value: 0,
        message: "Une salle doit avoir au moins une place ('seats')"
    )]
    #[Assert\NotBlank(message: "Le nombre de places ('seats') est obligatoire")]
    #[Groups(["room"])]
    private ?int $seats = null;

    #[ORM\Column]
    #[Groups(["room"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(["room"])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[ORM\JoinColumn(name: "cinema", referencedColumnName: "uid")]
    private ?Cinema $cinema = null;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Sceance::class)]
    #[ORM\JoinColumn(name: "sceances", referencedColumnName: "uid")]
    private Collection $sceances;

    public function __construct()
    {
        $this->uid = Uuid::v4();
        $this->sceances = new ArrayCollection();
    }

    public function getUid(): ?Uuid
    {
        return $this->uid;
    }

    public function setUid(Uuid $uid): static
    {
        $this->uid = $uid;

        return $this;
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

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function setSeats(int $seats): static
    {
        $this->seats = $seats;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCinema(): ?Cinema
    {
        return $this->cinema;
    }

    public function setCinema(?Cinema $cinema): static
    {
        $this->cinema = $cinema;

        return $this;
    }

    /**
     * @return Collection<int, Sceance>
     */
    public function getSceances(): Collection
    {
        return $this->sceances;
    }

    public function getSceance(UuidV4 $targetSceance): Sceance | null | Collection
    {
        return $this->getSceances()->filter(
            function ($sceance) use ($targetSceance) {
                return ($sceance->getUid() == $targetSceance);
            }
        )->first();
    }

    public function addSceance(Sceance $sceance): static
    {
        if (!$this->sceances->contains($sceance)) {
            $this->sceances->add($sceance);
            $sceance->setRoom($this);
        }

        return $this;
    }

    public function removeSceance(Sceance $sceance): static
    {
        if ($this->sceances->removeElement($sceance)) {
            // set the owning side to null (unless already changed)
            if ($sceance->getRoom() === $this) {
                $sceance->setRoom(null);
            }
        }

        return $this;
    }
}
