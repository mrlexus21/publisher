<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;
    #[ORM\Column(type: 'string', length: 255)]
    private string $slug;
    #[ORM\Column(type: 'string', length: 255)]
    private string $image;
    #[ORM\Column(type: 'simple_array')]
    private array $authors;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $publicationDate;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $meap;
    /**
     * @var Collection<BookCategory>
     */
    #[ORM\ManyToMany(targetEntity: BookCategory::class)]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Book
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): Book
    {
        $this->image = $image;

        return $this;
    }

    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function setAuthors(array $authors): Book
    {
        $this->authors = $authors;

        return $this;
    }

    public function getPublicationDate(): \DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): Book
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function isMeap(): bool
    {
        return $this->meap;
    }

    public function setMeap(bool $meap): Book
    {
        $this->meap = $meap;

        return $this;
    }

    /**
     * @return Collection<BookCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Collection<BookCategory> $categories
     *
     * @return $this
     */
    public function setCategories(Collection $categories): Book
    {
        $this->categories = $categories;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
