<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RoomRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 * @ApiResource()
 */
class Room extends Property
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups ({"read:announcement", "favs:read"})
     */
    private $ac;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *@Groups ({"read:announcement", "favs:read"})
     */
    private $roomBathroom;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups ({"read:announcement", "favs:read"})
     */
    private $balcony;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAc(): ?bool
    {
        return $this->ac;
    }

    public function setAc(bool $ac): self
    {
        $this->ac = $ac;

        return $this;
    }

    public function getRoomBathroom(): ?bool
    {
        return $this->roomBathroom;
    }

    public function setRoomBathroom(bool $bathroom): self
    {
        $this->roomBathroom = $bathroom;

        return $this;
    }

    public function getBalcony(): ?bool
    {
        return $this->balcony;
    }

    public function setBalcony(bool $balcony): self
    {
        $this->balcony = $balcony;

        return $this;
    }
}
