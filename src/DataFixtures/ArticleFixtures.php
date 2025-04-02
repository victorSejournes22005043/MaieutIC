<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $article = new Article();
            $article->setTitle("Article Title $i")
                ->setAuthor("Author $i")
                ->setLink("https://example.com/article-$i");
            $manager->persist($article);
        }

        $manager->flush();
    }
}
