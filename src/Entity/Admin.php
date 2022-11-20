<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AdminRepository;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 * @ApiResource(
 *  attributes = {
 *      "route_prefix" = "/bi_immo"
 *  },
 *  itemOperations={
 *      "get"= {
 *          "security"="is_granted('ROLE_ADMIN')",
 *          "security_message"="Désolé, vous n'avez pas accès à cette ressource.",
 *          "normalization_context"={"groups"={"user:read"}}
 *       },
 *      "put"={
 *          "security"="object == user",
 *          "security_message"="Désolé, vous n'avez pas accès à cette ressource.",
 *          "denormalization_context"={"groups"={"admin:write"}},
 *          "normalization_context"={"groups"={"user:read"}}
 *      }
 *  }
 * )
 */
class Admin extends User
{
}
