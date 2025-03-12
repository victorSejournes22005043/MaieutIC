<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@example.com')
             ->setUsername('user1')
             ->setPassword($this->passwordHasher->hashPassword($user, 'password'))
             ->setWhoami('I am a test user')
             ->setMyhobbies('Coding, Reading')
             ->setWhatimdoing('Learning Symfony')
             ->setMygoals('Become a proficient Symfony developer');

        $manager->persist($user);
        $manager->flush();

        // Add reference for other fixtures
        $this->addReference('user1', $user);
    }
}
