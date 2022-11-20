<?php

namespace App\DataPersister;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private $manager;
    private $encoder;

    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    public function supports($user, array $context = []): bool
    {
        return $user instanceof User;
    }

    public function persist($user, array $context = [])
    {
        if(!$context["item_operation_name"]){
            $password = $user->getPassword();
            $encodedPassword = $this->encoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
        }
        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function remove($user, array $context = [])
    {
        $user->setDeleted(true);
        $this->manager->persist($user);
        $this->manager->flush();
        return $user;
    }
}
