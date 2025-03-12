<?php

namespace App\DataFixtures;

use App\Entity\Like;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LikeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference('user1');

        $comment = new Comment();
        $comment->setBody('This is a comment.')
                ->setCreationDate(new \DateTime())
                ->setUserId($user);

        $like = new Like();
        $like->setUser($user)
             ->setComment($comment)
             ->setIsLike(true);

        $manager->persist($comment);
        $manager->persist($like);
        $manager->flush();
    }
}
