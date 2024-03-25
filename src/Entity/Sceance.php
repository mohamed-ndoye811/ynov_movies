<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SceanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SceanceRepository::class)]
#[ApiResource]
class Sceance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uid = null;

    #[ORM\Column(type: 'uuid')]
    #[Assert\Uuid]
    private ?Uuid $movie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Date]
    #[Assert\GreaterThanOrEqual(
        value: 'now',
        message: "You can't plan a sceance in the past"
    )]
    private ?\DateTimeInterface $date = null;

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

    public function getMovie(): ?Uuid
    {
        return $this->movie;
    }

    public function setMovie(Uuid $movie): static
    {
        $this->movie = $movie;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
