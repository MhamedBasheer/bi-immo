<?php

namespace App\Services;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FileService
{
    private $encoder, $serializer;

    public function __construct(UserPasswordEncoderInterface $encoder, DenormalizerInterface $serializer)
    {
        $this->encoder = $encoder;
        $this->serializer = $serializer;
    }

    public function getFile($request, $fileName, $type)
    {
        $uploadedFile = $request->files->get($fileName);
        if ($uploadedFile) {
            $ftype = explode("/", $uploadedFile->getMimeType())[0];
            if ((is_array($type) && in_array($ftype, $type)) || ($type == $ftype)) {
                $file = $uploadedFile->getRealPath();
                return fopen($file, 'r+');
            } else {
                $allowedTypes = is_array($type) ? implode(" ou, ", $type) : $type;
                throw new BadRequestHttpException("$fileName doit être un fichier de type $allowedTypes");
            }
        }
    }

    public function getFiles($uploadedFiles, $type){
        $files = [];
        foreach($uploadedFiles as $uploadedFile)
        if ($uploadedFile) {
            $ftype = $uploadedFile->getMimeType();
                if ((is_array($type) && in_array($ftype, $type)) || ($type == $ftype)) {
                    $file = $uploadedFile->getRealPath();
                    $files[] = fopen($file, 'r+');
                } else {
                    $allowedTypes = is_array($type) ? implode(" ou, ", $type) : $type;
                    throw new BadRequestHttpException("Les images doivent être du type $allowedTypes");
                }
            }
        return $files;
    }

    function putFormData($request, string $fileName, $fileType)
    {
        $raw = $request->getContent();
        $delimiter = "multipart/form-data; boundary=";
        $boundary = "--" . explode($delimiter, $request->headers->get("content-type"))[1];
        $elements = str_replace([$boundary, "Content-Disposition: form-data;", "name="], "", $raw);
        $elementsTab = explode("\r\n\r\n", $elements);
        $data = [];
        for ($i = 0; isset($elementsTab[$i + 1]); $i += 2) {
            $key = str_replace(["\r\n", ' "', '"'], '', $elementsTab[$i]);
            if (strchr($key, $fileName)) {
                $ftype = $this->fileType($key);
                if ((is_array($fileType) && in_array($ftype, $fileType)) || ($fileType == $ftype)) {
                    $stream = fopen('php://memory', 'r+');
                    fwrite($stream, $elementsTab[$i + 1]);
                    rewind($stream);
                    $data[$fileName] =  $stream;
                } else {
                    $allowedTypes = is_array($fileType) ? implode(" ou, ", $fileType) : $fileType;
                    throw new BadRequestHttpException("$fileName doit être un fichier de type $allowedTypes");
                }
            } else {
                $val = str_replace(["\r\n", "--"], '', $elementsTab[$i + 1]);
                $data[$key] =  $val;
            }
        }
        return $data;
    }

    public function fileType($idx)
    {
        $type = explode("Content-Type: ", $idx)[1];
        return explode("/", $type)[0];
    }
  
    public function autorisedFile($entity, $name, $request, $autorised)
    {
        $files = $request->files->get($name);
        if ($files) {
            foreach ($files as $value){
                $mimeType = $value->getMimeType();
                if (in_array($mimeType, $autorised)) {
                    $image = new Image;
                    $image->setFile(fopen($value->getRealPath(), "rb"));
                    $entity->addImage($image);
                }
                else{
                    return false;
                }
            }
        }
        return true;
    }
}

