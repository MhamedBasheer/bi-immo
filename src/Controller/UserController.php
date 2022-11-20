<?php

namespace App\Controller;

use App\Services\FileService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{    
    public function __construct(SerializerInterface $serializer, DenormalizerInterface $denormalizer, FileService $service, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator, EntityManagerInterface $em, RoleRepository $roleRepository)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->denormalizer = $denormalizer;
        $this->encoder = $encoder;
        $this->service = $service;
        $this->serializer = $serializer;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @Route(
     *  "api/bi-immo/clients", 
     *  methods="POST",
     *  name="addUser"
     * )
     */
    public function addClient(Request $request)
    {
        $client = $this->addUser($request, "client");
        if ($client->getAvatar())
            $client->setAvatar(base64_encode(stream_get_contents($client->getAvatar())));
        
        $role = $this->roleRepository->findOneBy(array('name' => 'CLIENT'));
        $client->setRole($role);
        $this->em->persist($client);
        $this->em->flush();
        return new JsonResponse($this->serializer->serialize($client, 'json', ['groups' => 'user:read']), Response::HTTP_OK, [], true);
    }

    public function addUser($req, $user)
    {
        // $userTab = $req->request->all();
        $userTab = json_decode($req->getContent(), true);
        $userTab['avatar'] = $this->service->getFile($req, 'avatar', 'image');
        $userTab['newsletter'] = (bool)@$userTab['newsletter'];
        
        $data = $this->denormalizer->denormalize($userTab, "App\\Entity\\" . ucfirst(strtolower($user)));
        $this->validator->validate($data);
        $data->setPassword($this->encoder->encodePassword($data, $userTab['password']));
        $data->setCreatedAt(new DateTime());
        // dd($data);

        return $data;
    }

    /**
     * @Route(path="api/users/{phoneNumber}/check", name="find_user_by_phoneNumber", methods="GET")
     */
    public function getUserByPhoneNumber(string $phoneNumber, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(array('phoneNumber' => $phoneNumber));
        if ($user) {
            return new JsonResponse($this->serializer->serialize($user, 'json', ['groups' => 'user:read']), Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND, [], true);
    }
}
