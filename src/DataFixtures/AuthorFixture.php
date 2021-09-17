<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuthorFixture extends Fixture
{

    const ITEMS_COUNT = 10001;

    public function __construct() {

        $this->faker = Factory::create('ru_RU');
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < self::ITEMS_COUNT; $i++) {
      	  $gender = $this->faker->randomElement(['male', 'female']);
      	  $author = new Author();
      	  $author->setName($this->faker->firstName() . ' ' . $this->faker->lastName());
      	  $manager->persist($author);
        }

        $manager->flush();
    }
}
