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
        // $user = $this->getReference('user1', User::class);
        $forumNames = [
            'General Discussion',
            'Announcements',
            'Feedback & Suggestions',
            'Off-Topic',
            'Technical Support',
            'Events & Meetups',
            'Resources & Tools',
            'Job Opportunities',
            'Community Projects',
            'Miscellaneous'
        ];

        // Create 10 forums with a reference to the user
        foreach ($forumNames as $i => $forumName) {
            $forum = new Forum();
            $forum->setTitle("$forumName")
                  ->setBody("This is forum number $i.")
                  ->setLastActivity(new \DateTime());

            $manager->persist($forum);
        }
        $manager->flush();

        $this->addReference('forum1', $forum);
    }
}
