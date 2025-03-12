<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference('user1', User::class);
        $post = $this->getReference('post1', Post::class);

        $comment = new Comment();
        $comment->setBody('This is a comment.')
                ->setCreationDate(new \DateTime())
                ->setUserId($user)
                ->setPostId($post);

        $manager->persist($comment);
        $manager->flush();

        // Add reference for other fixtures
        $this->addReference('comment1', $comment);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PostFixtures::class,
        ];
    }
}
