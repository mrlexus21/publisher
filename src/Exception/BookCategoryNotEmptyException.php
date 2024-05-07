<?php

namespace App\Exception;

class BookCategoryNotEmptyException extends \RuntimeException
{
    public function __construct(int $booksCount)
    {
        parent::__construct(sprintf('book category has %d books', $booksCount));
    }
}
