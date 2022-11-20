<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\AnnouncementRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;

/**
 * @ORM\Entity(repositoryClass=AnnouncementRepository::class)
 * @ApiFilter(BooleanFilter::class, properties={"property.rental": "exact", "property.forSale": "exact", "property.furnished": "exact"})
 * @ApiFilter(SearchFilter::class, properties={"property.propertyType.name": "exact", "property.address": "partial"})
 * @ApiFilter(NumericFilter::class, properties={"property.room": "exact"})
 * @ApiFilter(RangeFilter::class, properties={"property.salePrice": "exact", "property.rentalPrice": "exact"})
 * @ApiResource (
 *     routePrefix="/bi-immo",
 *     denormalizationContext={"groups"={"write:announcement"}},
 *      attributes={
 *          "pagination_enabled"=true,
 *          "pagination_items_per_page"=10
 *      },
 *      order={
 *          "createdAt"="DESC"
 *      },
 *      collectionOperations={
 *         "get"={
 *           "method"="get",
 *           "path"="/announcements",
 *           "normalization_context"={"groups"={"read:announcement"}}
 *          },
 *          "post"={
 *           "method"="post",
 *           "path"="/add/announcements",
 *          },
 *      },
 *      itemOperations={
 *         "get"={
 *                "path"="/announcement/{id}",
 *                "normalization_context"={"groups"={"read:announcement"}},
 *               },
 *         "put"={"path"="/edit/announcement/{id}"},
 *         "delete"={"path"="/announcement/{id}"},
 *     },
 * )
 */
class Announcement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"read:announcement", "favs:read", "user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     * @Assert\NotBlank(message="le titre de l'annonce est obligatoir")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"read:announcement", "favs:read","write:announcement","user:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Groups ({"read:announcement", "favs:read","user:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups ({"read:announcement", "favs:read","user:read"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     * @Groups ({"read:announcement", "favs:read","user:read"})
     */
    private $closed = false;

    /**
     * @ORM\OneToOne(targetEntity=Property::class, mappedBy="announcement", cascade={"persist", "remove"})
     * @Groups ({"read:announcement", "favs:read", "user:read"})
     * @Assert\NotBlank(message="le type de proprité de l'annonce est obligatoire")
     */
    private $property;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="announcements")
     * @ORM\JoinColumn(nullable=true)
     * @Groups ({"read:announcement", "favs:read"})
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archiver = false;

    /**
     * @ORM\ManyToMany(targetEntity=Client::class, mappedBy="favoris")
     */
    private $clients;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->clients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(Property $property): self
    {
        // set the owning side of the relation if necessary
        if ($property->getAnnouncement() !== $this) {
            $property->setAnnouncement($this);
        }

        $this->property = $property;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->addFavori($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->removeElement($client)) {
            $client->removeFavori($this);
        }

        return $this;
    }
}
