<?php

namespace App\DataFixtures;

use App\Entity\Resource;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ResourceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $resource = new Resource();
            $resource->setTitle("Resource Title $i")
                ->setDescription("Description for resource $i")
                ->setLink("https://example.com/resource-$i")
                ->setImage("https://example.com/image-$i.jpg");
            $manager->persist($resource);
        }

        $manager->flush();
    }
}
