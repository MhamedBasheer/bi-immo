<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\UserController;
use App\Repository\ClientRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @ApiResource(
 *  attributes = {
 *      "route_prefix" = "/bi-immo"
 *  },
 *  collectionOperations = {
 *      "post" = {
 *          "path" = "/clients",
 *          "deserialize" = false
 *      },
 *      "get" = {
 *          "path" = "clients",
 *          "security"="is_granted('ROLE_ADMIN')",
 *          "security_message"="Désolé, vous n'avez pas accès à cette ressource.",
 *          "normalization_context"={"groups"={"user:read"}}
 *      }
 *  },
 *  itemOperations={
 *      "get"={ 
 *          "path"="/client/{id}",
 *          "security_message"="Désolé, vous n'avez pas accès à cette ressource.",
 *          "normalization_context"={"groups"={"user:read"}}
 *      },
 *      "put"={
 *          "path"="/client/{id}",
 *          "security_message"="Désolé, vous n'avez pas accès à cette ressource.",
 *          "denormalization_context"={"groups"={"client:write"}},
 *          "normalization_context"={"groups"={"user:read"}}
 *      },
 *      "favoris"={
 *          "method"="get",
 *          "path"="/announcements/favs/{id}",
 *          "normalization_context"={"groups"={"favs:read"}}
 *      }
 *  }
 * )
 * @ApiFilter(SearchFilter::class, properties={"newsletter": "exact"})
 */
class Client extends User
{
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull(message="Vérifiez que tous les champs obligatoires ont été renseigné.")
     * @Groups({"user:read", "client:write"})
     */
    private $newsletter = false;

    /**
     * @ORM\ManyToMany(targetEntity=Announcement::class, inversedBy="clients")
     * @Groups({"user:read", "client:write", "favs:read"})
     */
    private $favoris;

    public function __construct()
    {
        parent::__construct();
        $this->favoris = new ArrayCollection();
    }

    public function getNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * @return Collection|Announcement[]
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Announcement $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
        }

        return $this;
    }

    public function removeFavori(Announcement $favori): self
    {
        $this->favoris->removeElement($favori);

        return $this;
    }
}
