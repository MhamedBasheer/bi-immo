<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PropertyRepository::class)
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({ "land" = "Land", "room" = "Room", "house" = "House", "apartment" = "Apartment"})
 * @ApiResource (
 *     routePrefix="/bi-immo",
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"write:property"}},
 *      collectionOperations={
 *         "get"=
 *             {
 *               "method"="get",
 *               "path"="/property",
 *               "normalization_context"={"groups"={"read:property"}},
 *              },
 *         "post"=
 *             {
 *               "method"="post",
 *               "path"="/properties/",
 *             }
 *      },
 *      itemOperations={
 *          "get"={
 *              "path"="/property/{id}",
 *              "normalization_context"={"groups"={"read:property"}}
 *          }
 *      }
 * )
 */

abstract class Property
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"read:announcement", "favs:read","user:read"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\Positive
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     * @Assert\NotBlank(message="La surface du bien est obligatoire")
     */
    private $surface;

    /**
     * @ORM\Column(type="float",nullable=true)
     * @Assert\Positive
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     */
    private $salePrice;

    /**
     * @ORM\Column(type="float",nullable=true)
     * @Assert\Positive
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     */
    private $rentalPrice;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     * @Assert\NotBlank(message="l'adresse du bien est obligatoir")
     */
    private $address;

    /**
     * @ORM\OneToOne(targetEntity=Announcement::class, inversedBy="property", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $announcement;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="property", orphanRemoval=true,cascade={"persist"})
     * @Groups ({"read:announcement", "favs:read","user:read", "read:property"})
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=PropertyType::class, inversedBy="properties")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     */
    private $propertyType;

    /**
     * @ORM\Column(type="boolean")
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     */
    private $forSale = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     */
    private $rental = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     */
    private $sold = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     */
    private $rented = false;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     */
    private $furnished = false;

    /**
     * @ORM\Column(type="float",nullable=true)
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="float",nullable=true)
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archiver = false;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSurface(): ?float
    {
        return $this->surface;
    }

    public function setSurface(float $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getSalePrice(): ?float
    {
        return $this->salePrice;
    }

    public function setSalePrice(float $salePrice): self
    {
        $this->salePrice = $salePrice;

        return $this;
    }

    public function getRentalPrice(): ?float
    {
        return $this->rentalPrice;
    }

    public function setRentalPrice(float $rentalPrice): self
    {
        $this->rentalPrice = $rentalPrice;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAnnouncement(): ?Announcement
    {
        return $this->announcement;
    }

    public function setAnnouncement(Announcement $announcement): self
    {
        $this->announcement = $announcement;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProperty($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProperty() === $this) {
                $image->setProperty(null);
            }
        }

        return $this;
    }

    public function getPropertyType(): ?PropertyType
    {
        return $this->propertyType;
    }

    public function setPropertyType(?PropertyType $propertyType): self
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    public function getForSale(): ?bool
    {
        return $this->forSale;
    }

    public function setForSale(bool $forSale): self
    {
        $this->forSale = $forSale;

        return $this;
    }

    public function getRental(): ?bool
    {
        return $this->rental;
    }

    public function setRental(bool $rental): self
    {
        $this->rental = $rental;

        return $this;
    }

    public function getSold(): ?bool
    {
        return $this->sold;
    }

    public function setSold(bool $sold): self
    {
        $this->sold = $sold;

        return $this;
    }

    public function getRented(): ?bool
    {
        return $this->rented;
    }

    public function setRented(bool $rented): self
    {
        $this->rented = $rented;

        return $this;
    }

    public function getFurnished(): ?bool
    {
        return $this->furnished;
    }

    public function setFurnished(bool $furnished): self
    {
        $this->furnished = $furnished;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getArchiver(): ?bool
    {
        return $this->archiver;
    }

    public function setArchiver(?bool $archiver): self
    {
        $this->archiver = $archiver;

        return $this;
    }
}
