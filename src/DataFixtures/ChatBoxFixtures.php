<?php

namespace App\DataFixtures;

use App\Entity\ChatBox;
use App\Entity\Forum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ChatBoxFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $forum = new Forum();
        $forum->setName('Forum1')
              ->setDescription('This is a forum.')
              ->setNbMembers(10)
              ->setLastActivity(new \DateTime());

        $chatBox = new ChatBox();
        $chatBox->setNbChat(0)
                ->setForum($forum);

        $manager->persist($forum);
        $manager->persist($chatBox);
        $manager->flush();

        // Add reference for other fixtures
        $this->addReference('forum1', $forum);
        $this->addReference('chatBox1', $chatBox);
    }
}
