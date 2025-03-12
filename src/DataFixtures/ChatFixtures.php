<?php

namespace App\DataFixtures;

use App\Entity\Chat;
use App\Entity\ChatBox;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ChatFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $chatBox = $this->getReference('chatBox1');
        $user = $this->getReference('user1');

        $chat = new Chat();
        $chat->setUser($user)
             ->setBody('This is a chat message.')
             ->setChatBox($chatBox);

        $manager->persist($chat);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ChatBoxFixtures::class,
            UserFixtures::class,
        ];
    }
}
