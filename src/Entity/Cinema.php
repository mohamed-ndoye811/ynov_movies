<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Attribute\HateoasLink;
use App\Repository\CinemaRepository;
use Doctrine\ORM\Mapping as ORM;
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

    public function __construct()
    {
//        $this->uid = Uuid::v4();
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
}
