<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
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

    public function __construct()
    {
        $this->uid = Uuid::v4();
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
}
