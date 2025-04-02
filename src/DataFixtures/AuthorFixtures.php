<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $author = new Author();
            $author->setName("Author $i")
                ->setBirthYear(1900 + $i)
                ->setDeathYear(2000 + $i)
                ->setNationality("Nationality $i")
                ->setLink("https://example.com/author-$i")
                ->setImage("https://example.com/image-$i.jpg");
            $manager->persist($author);
        }

        $manager->flush();
    }
}
