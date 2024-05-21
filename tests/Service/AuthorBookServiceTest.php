<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
use App\Entity\User;
use App\Exception\BookAlreadyExistsException;
use App\Model\Author\BookDetails;
use App\Model\Author\BookFormatOptions;
use App\Model\Author\BookListItem;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\Author\UpdateBookRequest;
use App\Model\Author\UploadCoverResponse;
use App\Model\BookCategory;
use App\Model\BookFormat;
use App\Model\IdResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookFormatRepository;
use App\Repository\BookRepository;
use App\Service\AuthorBookService;
use App\Service\UploadService;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class AuthorBookServiceTest extends AbstractTestCase
{
    private BookRepository $bookRepository;
    private BookFormatRepository $bookFormatRepository;
    private BookCategoryRepository $bookCategoryRepository;
    private SluggerInterface $slugger;
    private UploadService $uploadService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookFormatRepository = $this->createMock(BookFormatRepository::class);
        $this->bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $this->slugger = $this->createMock(SluggerInterface::class);
        $this->uploadService = $this->createMock(UploadService::class);
    }

    public function testUploadCover(): void
    {
        $file = new UploadedFile(
            'path',
            'cover',
            null,
            UPLOAD_ERR_NO_FILE,
            true
        );
        $book = (new Book())->setImage(null);
        $this->setEntityId($book, 1);

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('commit');

        $this->uploadService->expects($this->once())
            ->method('uploadBookFile')
            ->with(1, $file)
            ->willReturn('http://localhost/new.jpg');

        $this->assertEquals(
            new UploadCoverResponse('http://localhost/new.jpg'),
            $this->createService()->uploadCover(1, $file)
        );
    }

    public function testUploadCoverRemoveOld(): void
    {
        $file = new UploadedFile(
            'path',
            'cover',
            null,
            UPLOAD_ERR_NO_FILE,
            true
        );
        $book = (new Book())->setImage('http://localhost/old.jpg');
        $this->setEntityId($book, 1);

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('commit');

        $this->uploadService->expects($this->once())
            ->method('uploadBookFile')
            ->with(1, $file)
            ->willReturn('http://localhost/new.jpg');

        $this->uploadService->expects($this->once())
            ->method('deleteBookFile')
            ->with(1, 'old.jpg');

        $this->assertEquals(
            new UploadCoverResponse('http://localhost/new.jpg'),
            $this->createService()->uploadCover(1, $file)
        );
    }

    public function testDeleteBook(): void
    {
        $book = (new Book());
        $this->setEntityId($book, 1);

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('removeAndCommit')
            ->with($book);

        $this->createService()->deleteBook(1);
    }

    public function testGetBook(): void
    {
        $book = MockUtils::createBook();

        $category = MockUtils::createBookCategory();
        $this->setEntityId($category, 1);

        $format = MockUtils::createBookFormat();
        $this->setEntityId($format, 1);

        $book->setCategories(new ArrayCollection([$category]));
        $bookLink = MockUtils::createBookFormatLink($book, $format);
        $book->setFormats(new ArrayCollection([$bookLink]));

        $this->setEntityId($book, 1);

        $bookDetail = (new BookDetails())
            ->setId(1)
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setImage('http://localhost.png')
            ->setIsbn('123456')
            ->setDescription('test')
            ->setPublicationDate(1602288000)
            ->setAuthors(['Tester'])
            ->setCategories([new BookCategory(1, 'Devices', 'devices')])
            ->setFormats([
                (new BookFormat())
                    ->setId(1)
                    ->setDescription('description format')
                    ->setPrice(123.55)
                    ->setDiscountPercent(5)
                    ->setTitle('format')
                    ->setComment(null),
            ]);

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->assertEquals($bookDetail, $this->createService()->getBook(1));
    }

    public function testGetBooks(): void
    {
        $user = MockUtils::createUser();

        $book = MockUtils::createBook();
        $this->setEntityId($book, 1);

        $this->bookRepository->expects($this->once())
            ->method('findUserBooks')
            ->with($user)
            ->willReturn([$book]);

        $bookList = new BookListResponse([(new BookListItem())
            ->setId(1)
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setImage('http://localhost.png'),
        ]);

        $this->assertEquals($bookList, $this->createService()->getBooks($user));
    }

    public function testCreateBook(): void
    {
        $payload = new CreateBookRequest();
        $payload->setTitle('New book');

        $user = new User();

        $book = (new Book())->setTitle('New book')
            ->setSlug('new-book')
            ->setUser($user);

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('New book')
            ->willReturn(new UnicodeString('new-book'));

        $this->bookRepository->expects($this->once())
            ->method('existBySlug')
            ->with('new-book')
            ->willReturn(false);

        $this->bookRepository->expects($this->once())
            ->method('saveAndCommit')
            ->with($book)
            ->willReturnCallback(function (Book $book) {
                $this->setEntityId($book, 1);
            });

        $this->assertEquals(new IdResponse(1), $this->createService()->createBook($payload, $user));
    }

    public function testCreateBookSlugExistException(): void
    {
        $this->expectException(BookAlreadyExistsException::class);

        $payload = new CreateBookRequest();
        $payload->setTitle('New book');

        $user = new User();

        $book = (new Book())->setTitle('New book')
            ->setSlug('new-book')
            ->setUser($user);

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('New book')
            ->willReturn(new UnicodeString('new-book'));

        $this->bookRepository->expects($this->once())
            ->method('existBySlug')
            ->with('new-book')
            ->willReturn(true);

        $this->createService()->createBook($payload, $user);
    }

    public function testUpdateBookExceptionOnDuplicateSlug(): void
    {
        $this->expectException(BookAlreadyExistsException::class);

        $payload = new UpdateBookRequest();
        $payload->setTitle('Old');

        $book = (new Book());

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('Old')
            ->willReturn(new UnicodeString('old'));

        $this->bookRepository->expects($this->once())
            ->method('existBySlug')
            ->with('old')
            ->willReturn(true);

        $this->createService()->updateBook(1, $payload);
    }

    public function testUpdateBook(): void
    {
        $book = (new Book());
        $bookToBookFormat = new BookToBookFormat();
        $book->setFormats(new ArrayCollection([$bookToBookFormat]));

        $category = MockUtils::createBookCategory();
        $this->setEntityId($category, 1);

        $format = MockUtils::createBookFormat();
        $this->setEntityId($format, 1);

        $newBookToBook = (new BookToBookFormat())
            ->setBook($book)
            ->setFormat($format)
            ->setPrice(123.5)
            ->setDiscountPercent(5);

        $payload = (new UpdateBookRequest())
            ->setTitle('Old')
            ->setAuthors(['Tester'])
            ->setIsbn('isbn')
            ->setCategories([1])
            ->setFormats([
                (new BookFormatOptions())
                ->setId(1)
                ->setPrice(123.5)
                ->setDiscountPercent(5),
            ])
            ->setDescription('description');

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('Old')
            ->willReturn(new UnicodeString('old'));

        $this->bookRepository->expects($this->once())
            ->method('existBySlug')
            ->with('old')
            ->willReturn(false);

        $this->bookCategoryRepository->expects($this->once())
            ->method('findBookCategoriesByIds')
            ->with([1])
            ->willReturn([$category]);

        $this->bookFormatRepository->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($format);

        $this->bookRepository->expects($this->once())
            ->method('saveBookFormatReference')
            ->with($newBookToBook);

        $this->bookRepository->expects($this->once())
            ->method('removeBookFormatReference')
            ->with($bookToBookFormat);

        $this->bookRepository->expects($this->once())
            ->method('commit');

        $this->createService()->updateBook(1, $payload);
    }

    public function createService(): AuthorBookService
    {
        return new AuthorBookService(
            $this->bookRepository,
            $this->bookFormatRepository,
            $this->bookCategoryRepository,
            $this->slugger,
            $this->uploadService
        );
    }
}
