<?php

namespace App\Tests\Controller;

use App\Controller\BookCategoryController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookCategoryControllerTest extends WebTestCase
{

    public function testCategories()
    {
        $client = self::createClient();
        $client->request('GET', '/api/v1/book/categories');
        $responseContent = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '\responses\BookCategoryControllerTest_testCategories.json',
            $responseContent
        );
    }
}
