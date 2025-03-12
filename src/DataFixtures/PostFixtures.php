<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Tag;
use App\Entity\Forum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference('user1');
        $tag = new Tag();
        $tag->setName('Tag1');

        $forum = $this->getReference('forum1');

        $post = new Post();
        $post->setTitle('Post Title')
             ->setBody('This is the body of the post.')
             ->setUser($user)
             ->setCreationDate(new \DateTime())
             ->setLastActivity(new \DateTime())
             ->setNbComments(0)
             ->addTag($tag)
             ->setForum($forum);

        $manager->persist($tag);
        $manager->persist($post);
        $manager->flush();

        // Add reference for other fixtures
        $this->addReference('post1', $post);
    }

    public function getDependencies(): array
    {
        return [
            ForumFixtures::class,
        ];
    }
}
