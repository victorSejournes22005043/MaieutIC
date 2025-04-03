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

        $author = new Author();
        $author->setName("Pierre Bourdieu")
            ->setBirthYear(1930)
            ->setDeathYear(2002)
            ->setNationality("French")
            ->setLink("https://en.wikipedia.org/wiki/Pierre_Bourdieu")
            ->setImage("https://upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Pierre_Bourdieu_2001.jpg/800px-Pierre_Bourdieu_2001.jpg");
            
        $manager->persist($author);

        $manager->flush();
    }
}
