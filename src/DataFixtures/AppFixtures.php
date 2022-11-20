<?php

namespace App\DataFixtures;

use App\Entity\PropertyType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach(["apartment", "house", "land", "room", "shop"] as $ptype){
            $property_type = new PropertyType();
            $property_type->setName($ptype);
            $manager->persist($property_type);
        }
        $manager->flush();
    }
}
