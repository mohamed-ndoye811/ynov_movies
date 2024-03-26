<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RoomRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Types\UuidType;
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
class Room
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
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

    public function __construct()
    {
        $this->uid = Uuid::v4();
    }

    public function getId(): ?int
    {
        return $this->id;
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
}
