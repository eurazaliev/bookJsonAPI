<?php

namespace App\DataFixtures;

use App\Entity\{Author, Book};

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Faker\Factory;

class BookFixture extends Fixture
{

    const ITEMS_COUNT = 10001;

    public function __construct() {

        $this->fakerRu = Factory::create('ru_RU');
        $this->fakerEn = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < self::ITEMS_COUNT; $i++) {

            $book = new Book();

            $book->setAuthor(
                $manager->getRepository(Author::class)->find(rand(1, AuthorFixture::ITEMS_COUNT))
            );

            //$this->fakerEn->seed($i);
            $nameEn = $this->fakerEn->sentence(rand(1, 5));
            //$this->fakerRu->seed($i);
            $arr = array($this->fakerRu->firstName(), $this->fakerRu->company(), $this->fakerRu->catchPhrase());
            shuffle($arr);
            $nameRu =  implode(' ', $arr);

            $book->translate('en')->setName($nameEn);
            $book->translate('ru')->setName($nameRu);
            $manager->persist($book);
            $book->mergeNewTranslations();
        }

        $manager->flush();
    }
}
