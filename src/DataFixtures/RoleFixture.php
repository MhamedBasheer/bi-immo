<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class RoleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roles = ['admin', 'client'];
        foreach ($roles as $name) {
            $role = new Role();
            $role->setName($name);
            $manager->persist($role);
            $manager->flush();
            $this->addReference($name, $role);
        }
    }
}
