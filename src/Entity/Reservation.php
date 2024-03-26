<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = "expr('/reservation/' ~ object.getUid())",
 *      exclusion = @Hateoas\Exclusion(groups="reservation")
 * )
 */
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource]
class Reservation
{
    public const STATUS_OPEN = 'open';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS = [self::STATUS_OPEN, self::STATUS_CONFIRMED, self::STATUS_CONFIRMED];

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $uid = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\Range(
        min: 0,
        notInRangeMessage: 'The rank cannot be lower than 0',
    )]
    #[Assert\NotBlank(message: "Le nom du cinéma est obligatoire")]
    #[Groups(["reservation"])]
    private ?int $rank = null;

    #[ORM\Column(length: 16, type: 'string')]
    #[Assert\Choice(
        choices: Reservation::STATUS,
        message: "Choose a valid status"
    )]
    #[Assert\NotBlank(message: "Le nom du cinéma est obligatoire")]
    #[Groups(["reservation"])]
    private ?string $status = null;

    #[ORM\Column]
    #[Assert\GreaterThan(
        value: 1,
        message: "A reservation must have at least 1 seat to lock"
    )]
    #[Assert\NotBlank(message: "Le nombre de places est obligatoire")]
    #[Groups(["reservation"])]
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

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(int $rank): static
    {
        $this->rank = $rank;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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
