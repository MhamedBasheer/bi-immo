<?php

namespace App\Services;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ErrorServices
{
    function existEntity ($entity ,$message)
    {
        if (!$entity) {
            return new JsonResponse(json_decode($message), Response::HTTP_BAD_REQUEST, [], true);
        }
       return $entity[0];
    }
}
