<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Announcement;
use App\Entity\Profil;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class AnnouncementDataPersister implements DataPersisterInterface
{

    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    public function supports($data): bool
    {
        return $data instanceof Announcement;
    }
    public function persist($data)
    {
        dd($data);
        $this->manager->persist($data);
        $this->manager->flush();
    }
    public function remove($data)
    {
        $data->setArchiver(true);
        $data->getProperty()->setArchiver(true);
        $this->manager->flush();
    }
}
