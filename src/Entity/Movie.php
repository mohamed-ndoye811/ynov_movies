<?php

namespace App\Entity;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_movie",
 *          parameters = { "uid" = "expr(object.getUid())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="movie")
 * )
 *
 */
#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => 'movie:read']
        ),
        new Patch(inputFormats: ['json' => ['application/merge-patch+json']]),
    ],
    formats: ['jsonld', 'json', 'html', 'jsonhal', 'csv' => ['text/csv']]
)]
class Movie
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(["movie"])]
    private ?string $uid = null;

    #[ORM\Column(length: 128, nullable: false)]
    #[Groups(["movie", "movie:read"])]
    private ?string $name = null;

    #[ORM\Column(type: "text", length: 4095, nullable: false)]
    #[Groups(["movie"])]
    private ?string $description = null;

    #[ORM\Column(type: "integer", nullable: false)]
    #[Groups(["movie"])]
    private ?int $rate = null;

    #[ORM\Column(type: "integer", nullable: false)]
    #[Assert\Range(
            min: 1,
            max: 240,
            notInRangeMessage: 'Le film doit durer {{ min }} minute minimum and {{ max }} minutes maximum pour être enregistré',
        )]
    #[Groups(["movie"])]
    private ?int $duration = null;

    public function __construct()
    {

    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(?int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMovieBody() {
        return $this->movie_body;
    }
}
