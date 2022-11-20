<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImageRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ApiResource (
 *  routePrefix="/bi-immo",
 *  normalizationContext={"groups"={"read:property"}},
 *  attributes={
 *      "pagination_enabled"=true,
 *      "pagination_items_per_page"=10
 *  },
 *  itemOperations={
 *      "get"={
 *          "path"="/images/{id}"
 *      },
 *      "delete"={"path"="/images/{id}"},
 *  }
 * )
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:announcement", "favs:read","user:read", "read:property"})
     */
    private $id;

    /**
     * @ORM\Column(type="blob", nullable=true)
     * @Groups({"user:read", "read:property"})
     */
    private $file;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="profil_image", fileNameProperty="fileName")
     *
     * @var File|null
     *private $imageFile;
     */
    /**
     * @param File|null $imageFile
     * @return File|null
     *
     * public function getImageFile(): ?File
     * {
     * return $this->imageFile;
     * }
     *
     * /**
     * @return Image
     *
     * public function setImageFile(?File $imageFile): Image
     * {
     * $this->imageFile = $imageFile;
     * if (null !== $imageFile) {
     * // It is required that at least one field changes if you are using doctrine
     * // otherwise the event listeners won't be called and the file is lost
     * $this->updatedAt = new \DateTimeImmutable();
     * }
     * return $this;
     * }
     */
    /**
     * @ORM\ManyToOne(targetEntity=Property::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $property;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile()
    {
        if ($this->file) {
            return base64_encode(stream_get_contents($this->file));
        }
        return null;
    }
    
    public function setFile($file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): self
    {
        $this->property = $property;

        return $this;
    }
}
