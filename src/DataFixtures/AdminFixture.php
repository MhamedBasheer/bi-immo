<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\DataFixtures\RoleFixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\adminPasswordEncoderInterface;

class AdminFixture extends Fixture implements DependentFixtureInterface
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $role = $this->getReference("admin");
        $admin = new Admin();
        $password = $this->encoder->encodePassword($admin, 'pass1234');
        $admin -> setFirstname('admin')
            -> setRole($role)
            -> setLastname('admin')
            -> setEmail('admin@admin.sn')
            -> setPhoneNumber('786543212')
            -> setPassword($password);
        $manager->persist($admin);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            RoleFixture::class,
        );
    }
}
