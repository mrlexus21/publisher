<?php

namespace App\Model\Author;

class UpdateBookRequest
{
    public ?string $title = null;
    public ?array $authors = [];
    private ?string $isbn = null;
    private ?string $description = null;
    /** @var int[]|null */
    private ?array $categories = [];
    /** @var BookFormatOptions[]|null */
    private ?array $formats = [];

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthors(): ?array
    {
        return $this->authors;
    }

    public function setAuthors(?array $authors): self
    {
        $this->authors = $authors;

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

    public function getCategories(): ?array
    {
        return $this->categories;
    }

    public function setCategories(?array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getFormats(): ?array
    {
        return $this->formats;
    }

    public function setFormats(?array $formats): self
    {
        $this->formats = $formats;

        return $this;
    }
}
