<?php

namespace App\DataFixtures;

use App\Entity\Forum;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ForumFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference('user1', User::class);
        $forum = new Forum();
        $forum->setTitle('Forum1')
              ->setBody('This is a forum.')
              ->setLastActivity(new \DateTime());

        $manager->persist($forum);
        $manager->flush();

        $this->addReference('forum1', $forum);
    }
}
