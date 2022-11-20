<?php

namespace App\Entity;

use App\Repository\HouseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=HouseRepository::class)
 */
class House extends Property
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(message="le nombre d'étage est obligatoir pour une maisson")
     */
    private $floor;

    /**
     * @ORM\Column(type="integer")
     *@Assert\NotBlank(message="le nombre de chambre est obligatoir pour une maisson")
     */
    private $room;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *@Assert\NotBlank(message="le nombre de sall de bain est obligatoir pour une maisson")
     */
    private $bathroom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *@Assert\NotBlank(message="le nombre de sallon est obligatoir pour une maisson")
     */
    private $livingRoom;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getRoom(): ?int
    {
        return $this->room;
    }

    public function setRoom(int $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getBathroom(): ?int
    {
        return $this->bathroom;
    }

    public function setBathroom(int $bathroom): self
    {
        $this->bathroom = $bathroom;

        return $this;
    }

    public function getLivingRoom(): ?int
    {
        return $this->livingRoom;
    }

    public function setLivingRoom(int $livingRoom): self
    {
        $this->livingRoom = $livingRoom;

        return $this;
    }
}
