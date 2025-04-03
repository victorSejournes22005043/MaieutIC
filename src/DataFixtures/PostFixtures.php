<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Forum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference('user1', User::class);

        $forum = $this->getReference('forum1', Forum::class);

        $post = new Post();
        $post->setName('Post Title')
             ->setDescription('This is the body of the post.')
             ->setUser($user)
             ->setCreationDate(new \DateTime())
             ->setLastActivity(new \DateTime())
             ->setForum($forum);

        $manager->persist($post);
        $manager->flush();

        $this->addReference('post1', $post);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ForumFixtures::class,
        ];
    }
}
