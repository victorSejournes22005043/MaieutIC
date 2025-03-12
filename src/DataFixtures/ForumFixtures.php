<?php

namespace App\DataFixtures;

use App\Entity\Forum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ForumFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $forum = new Forum();
        $forum->setName('Forum1')
              ->setDescription('This is a forum.')
              ->setNbMembers(10)
              ->setLastActivity(new \DateTime());

        $manager->persist($forum);
        $manager->flush();
    }
}
