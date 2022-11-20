<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\Image;
use App\Services\FileService;
use App\Services\ErrorServices;
use App\Services\AnnouncementServices;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AnnouncementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;

class AnnouncementController extends AbstractController
{

    private $errorSrv;
    private $fileSrv;
    private $manager;
    private $validator;
    private $security;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $manager, FileService $fileSrv, ErrorServices $errorSrv, SerializerInterface $serializer, Security $security)
    {
        $this->serializer = $serializer;
        $this->errorSrv = $errorSrv;
        $this->fileSrv = $fileSrv;
        $this->validator = $validator;
        $this->manager = $manager;
        $this->security = $security;
    }

    /**
     * @Route(path={"/api/bi-immo/add/announcements"},methods={"POST"})
     */
    public function addAnnouncement(Request $request, AnnouncementServices $announcementSrv)
    {
        $data = $this->serializer->normalize(json_decode($request->getContent()));
        $announcement = $announcementSrv->addAnnounce($data);
        if ($this->fileSrv->autorisedFile($announcement->getProperty(), "imageFile", $request, ["image/png", "image/jpg", "image/jpeg"]) === false) {
            return new JsonResponse(json_encode('L\'image non prise en charge, seuls les formats png et jpg sont autorisés '), Response::HTTP_OK, [], true);
        }
        $notValid = $this->validate($announcement) ? $this->validate($announcement) : $this->validate($announcement->getProperty());
        if ($notValid) {
            return new JsonResponse($notValid, 400, [], true);
        }
        if($this->security->getUser()){
            $announcement->setUser($this->security->getUser());
        }
        $this->manager->persist($announcement);
        $this->manager->flush();
        return new JsonResponse($this->serializer->serialize($announcement, 'json', ['groups' => 'read:announcement']), Response::HTTP_OK, [], true);
    }

    private function validate($obj)
    {
        $errors = $this->validator->validate($obj);
        if (count($errors) > 0) {
            $errors = $this->serializer->serialize($errors, "json");
            return $errors;
        }
    }

    /**
     * @Route(path={"/api/bi-immo/edit/announcement/{id}"},methods={"PUT"})
     */
    public function EditAnnouncement(int $id, Request $request, AnnouncementServices $announcementSrv, SerializerInterface $serializer, AnnouncementRepository  $announcementRep)
    {
        $announcement = $announcementRep->findById($id);
        $announcement = $this->errorSrv->existEntity($announcement, "une annonce avec $id n'existe pas");
        $announcementSrv->EditAnnounce($request, $announcement);
        if ($this->fileSrv->autorisedFile($announcement->getProperty(), "imageFile", $request, ["image/png", "image/jpg", "image/jpeg"]) === false) {
            return new JsonResponse(json_encode('l\'image non prise en charge, seul les format png et jpg sont autorisé '), Response::HTTP_OK, [], true);
        }
        $errors = $this->validator->validate($announcement->getProperty());
        if (count($errors) > 0) {
            $errors = $serializer->serialize($errors, "json");
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST, [], true);
        }
        $this->manager->persist($announcement);
        $this->manager->flush();
        return new JsonResponse($this->serializer->serialize($announcement, 'json', ['groups' => 'read:announcement']), Response::HTTP_OK, [], true);
    }
}
