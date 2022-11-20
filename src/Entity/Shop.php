<?php

namespace App\Entity;

use App\Entity\Property;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ShopRepository;

/**
 * @ORM\Entity(repositoryClass=ShopRepository::class)
 */
class Shop extends Property
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
