<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

use App\Entity\{Author, Book};

class AuthorTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    public function testGetId()
    {
        $author = new Author();

        $this->assertNull($author->getId());
    }

    public function testSetName()
    {
        $author = new Author();

        $testName = 'Alexander Pushkin';
        $this->assertInstanceOf(Author::class, $author->setName($testName));
    }

    public function testGetName()
    {
        $author = new Author();

        $testName = 'Alexander Pushkin';
        $author->setName($testName);

        $this->assertEquals($testName, $author->getName($testName));
    }

    public function testGetBooks()
    {
        $author = new Author();

        $this->assertInstanceOf(ArrayCollection::class, $author->getBooks());
        $this->assertEquals(0, $author->getBooks()->count());

    }

    public function testAddBookNull()
    {
        $author = new Author();
        $this->expectException(\ArgumentCountError::class);
        $author->addBook();
    }

    public function testAddBookString()
    {
        $author = new Author();
        $this->expectException(\TypeError::class);
        $author->addBook();
    }

    public function testAddBookStdClass()
    {
        $author = new Author();
        $this->expectException(\TypeError::class);
        $author->addBook(new \StdClass());
    }

    public function testAddBook()
    {
        $author = new Author();
        $book = new Book();

        $this->assertInstanceOf(Author::class, $author->addBook($book));
        $this->assertEquals(1, $author->getBooks()->count());
    }

    public function testRemoveBook()
    {
        $author = new Author();
        $book = new Book();
        $author->addBook($book);

        $this->assertInstanceOf(Author::class, $author->removeBook($book));
        $this->assertEquals(0, $author->getBooks()->count());
    }

}


