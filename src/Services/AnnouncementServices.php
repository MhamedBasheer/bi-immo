<?php

namespace App\Services;

use App\Entity\Announcement;
use App\Repository\ImageRepository;
use App\Repository\PropertyTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\Image;

class AnnouncementServices
{

    private $serializer;
    private $propertyTypeRep;
    private $imageRep;

    public function __construct(ImageRepository $imageRep, PropertyTypeRepository $propertyTypeRep, DenormalizerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->propertyTypeRep = $propertyTypeRep;
        $this->imageRep = $imageRep;
    }

    function addAnnounce($data)
    {
        $propertyType = $this->propertyTypeRep->findOneBy(["name" => $data['propertyType']]);
        $class = "App\\Entity\\" . $propertyType->getName();
        if (class_exists($class)) {
            $announcement = $this->serializer->denormalize($data, Announcement::class);
            $property = new $class();
            $data["property"]['rental'] =  @$data["property"]['rentalPrice'] ? true : false;
            $data["property"]['forSale'] =  @$data["property"]['salePrice'] ? true : false;
            if(!$data["property"]['rental'] && !$data["property"]['forSale']){
                throw new BadRequestHttpException("Déterminer si la propriété est à louer ou à vendre");
            }
            $this->setValue($data['property'], $property);
            $property->setPropertyType($propertyType);
            $announcement->setProperty($property);
            return $announcement;
        }
        throw new BadRequestHttpException("Type de propriété inéxistant!");
    }

    function EditAnnounce($request, $entity)
    {
        $data = $request->request->all();
        foreach ($data as $key => $value) {
            if ($key == "property")
                foreach ($value as $key2 => $value2) {
                    $set = "set" . ucfirst($key2);
                    $entity->getProperty()->$set($value2);
                }
            else if ($key != "_method" && $key != "removeImage") {
                $set = "set" . ucfirst($key);
                $entity->$set($value);
            }
        }
        if (isset($data['removeImage'])) {
            foreach ($data['removeImage'] as $id) {
                $image = $this->imageRep->find($id);
                if ($image) {
                    $entity->getProperty()->removeImage($image);
                }
            }
        }
    }

    function setValue($data, $entity)
    {
        foreach ($data as $key => $value) {
            if($key == 'images'){
                foreach($value as $img){
                    $stream = tmpfile();
                    fwrite($stream, base64_decode($img));
                    rewind($stream);
                    // dd(file(stream_get_meta_data($stream)['uri']));
                    $image = new Image();
                    $image->setFile($stream);
                    $entity->addImage($image);
                }
            }else{
                $set = "set" . ucfirst($key);
                if(method_exists($entity, $set)){
                    $nombre = ($key == "address") ? $value : (float)$value;
                    $entity->$set($nombre);
                }
            }
        }
    }
}
