<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $book = new Book();
            $book->setTitle("Book Title $i")
                ->setAuthor("Author $i")
                ->setLink("https://example.com/book-$i")
                ->setImage("https://example.com/image-$i.jpg");
            $manager->persist($book);
        }

        $manager->flush();
    }
}