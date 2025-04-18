<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserLike;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LikeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference('user1', User::class);

        $comment = $this->getReference('comment1', Comment::class);

        $userLike = new UserLike();
        $userLike->setUser($user)
             ->setComment($comment);

        $manager->persist($comment);
        $manager->persist($userLike);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CommentFixtures::class,
        ];
    }
}
