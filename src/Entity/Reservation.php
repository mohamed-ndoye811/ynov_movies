<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource]
class Reservation
{
    public const STATUS_OPEN = 'open';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS = [self::STATUS_OPEN, self::STATUS_CONFIRMED, self::STATUS_CONFIRMED];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uid = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 0,
        notInRangeMessage: 'The rank cannot be lower than 0',
    )]
    private ?int $rank = null;

    #[ORM\Column(length: 16)]
    #[Assert\Choice(
        choices: Reservation::STATUS,
        message: "Choose a valid status"
    )]
    private ?string $status = null;

    #[ORM\Column]
    #[Assert\GreaterThan(
        value: 1,
        message: "A reservation must have at least 1 seat to lock"
    )]
    private ?int $seats = null;

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
