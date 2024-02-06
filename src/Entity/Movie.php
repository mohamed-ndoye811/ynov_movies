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

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_movie",
 *          parameters = { "id" = "expr(object.getId())" }
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
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["movie"])]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    #[Groups(["movie", "movie:read"])]
    private ?string $nom = null;

    #[ORM\Column(type: "text", length: 4095)]
    #[Groups(["movie"])]
    private ?string $description = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(["movie"])]
    private ?\DateTimeInterface $dateDeParution = null;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Groups(["movie"])]
    private ?int $rate = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["movie"])]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'movies')]
    #[Groups(["movie"])]
    private Collection $category;

    public function __construct()
    {
        $this->category = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

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

    public function getDateDeParution(): ?\DateTimeInterface
    {
        return $this->dateDeParution;
    }

    public function setDateDeParution(?\DateTimeInterface $dateDeParution): self
    {
        $this->dateDeParution = $dateDeParution;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->category->removeElement($category);

        return $this;
    }

    public function setCategory(Collection $category): static
    {
        $this->category = $category;

        return $this;
    }
}
