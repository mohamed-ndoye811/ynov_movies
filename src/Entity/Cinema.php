<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Attribute\HateoasLink;
use App\Repository\CinemaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = "expr('/cinema/' ~ object.getUid())",
 *      exclusion = @Hateoas\Exclusion(groups="cinema")
 * )
 */
#[ORM\Entity(repositoryClass: CinemaRepository::class)]
#[ApiResource]
#[ORM\HasLifecycleCallbacks]
#[HateoasLink("update", "PUT", "api_pets_put_item")]
class Cinema
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(["cinema"])]
    private ?Uuid $uid = null;

    #[ORM\Column(type: 'string', length: 128)]
    #[Assert\NotBlank(message: "Le nom du cinéma est obligatoire")]
    #[Groups(["cinema"])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(["cinema"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(["cinema"])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'cinema', targetEntity: Room::class)]
    #[ORM\JoinColumn(name: "rooms", referencedColumnName: "uid")]
    private Collection $rooms;

    public function __construct()
    {
//        $this->uid = Uuid::v4();
        $this->rooms = new ArrayCollection();
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

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function getRoom(UuidV4 $targetRoom): Room | null
    {
        return $this->getRooms()->filter(
            function ($room) use ($targetRoom) {
                return ($targetRoom === $room->getUid());
            }
        )[0];
    }

    public function addRoom(Room $room): static
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            $room->setCinema($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getCinema() === $this) {
                $room->setCinema(null);
            }
        }

        return $this;
    }
}
