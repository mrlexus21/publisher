<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

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
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image;
    #[ORM\Column(type: 'simple_array', nullable: true)]
    private ?array $authors;
    #[ORM\Column(type: 'string', length: 13, nullable: true)]
    private ?string $isbn;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;
    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeInterface $publicationDate;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $meap;
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private UserInterface $user;

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }
    /**
     * @var Collection<BookCategory>
     */
    #[ORM\ManyToMany(targetEntity: BookCategory::class)]
    #[ORM\JoinTable(name: 'book_to_book_category')]
    private Collection $categories;
    /**
     * @var Collection<BookToBookFormat>
     */
    #[ORM\OneToMany(targetEntity: BookToBookFormat::class, mappedBy: 'book')]
    private Collection $formats;
    /**
     * @var Collection<Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'book')]
    private Collection $reviews;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->formats = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): Book
    {
        $this->image = $image;

        return $this;
    }

    public function getAuthors(): ?array
    {
        return $this->authors;
    }

    public function setAuthors(?array $authors): Book
    {
        $this->authors = $authors;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?\DateTimeInterface $publicationDate): Book
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

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

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

    public function getFormats(): Collection
    {
        return $this->formats;
    }

    public function setFormats(Collection $formats): self
    {
        $this->formats = $formats;

        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function setReviews(Collection $reviews): self
    {
        $this->reviews = $reviews;

        return $this;
    }
}
