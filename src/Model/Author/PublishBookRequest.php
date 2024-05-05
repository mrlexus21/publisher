<?php

namespace App\Model\Author;

use Symfony\Component\Validator\Constraints\NotBlank;

class PublishBookRequest
{
    #[NotBlank]
    private \DateTimeInterface $date;

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
