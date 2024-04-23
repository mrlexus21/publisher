<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Model\RecommendedBook;
use App\Model\RecommendedBookListResponse;
use App\Repository\BookRepository;
use App\Service\Recommendation\Model\RecommendationItem;
use App\Service\Recommendation\Model\RecommendationResponse;
use App\Service\Recommendation\RecommendationApiService;
use App\Service\RecommendationService;
use App\Tests\AbstractTestCase;

class RecommendationServiceTest extends AbstractTestCase
{
    private BookRepository $bookRepository;
    private RecommendationApiService $recommendationApiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->recommendationApiService = $this->createMock(RecommendationApiService::class);
    }

    public static function dataProvider(): array
    {
        return [
            ['short description', 'short description'],
            [
                <<<EOF
begin long description long description
long description long description long
long description long description
long description long description
description
EOF,
                <<<EOF
begin long description long description
long description long description long
long description long description
long description long description
...
EOF,
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetRecommendationsByBookId(string $actualDescription, string $expectionDescription): void
    {
        $entity = (new Book())
            ->setTitle('title')
            ->setImage('image')
            ->setSlug('slug')
            ->setDescription($actualDescription);
        $this->setEntityId($entity, 2);

        $this->bookRepository->expects($this->once())
            ->method('findBooksById')
            ->with([2])
            ->willReturn([$entity]);

        $this->recommendationApiService->expects($this->once())
            ->method('getRecommendationByBookId')
            ->with(1)
            ->willReturn(new RecommendationResponse(1, 12345, [new RecommendationItem(2)]));

        $expected = new RecommendedBookListResponse([
            (new RecommendedBook())
                ->setTitle('title')
                ->setImage('image')
                ->setSlug('slug')
                ->setShortDescription($expectionDescription)
                ->setId(2),
        ]);

        $this->assertEquals(
            $expected,
            $this->createService()->getRecommendationsByBookId(1)
        );
    }

    private function createService(): RecommendationService
    {
        return new RecommendationService(
            $this->bookRepository,
            $this->recommendationApiService
        );
    }
}
