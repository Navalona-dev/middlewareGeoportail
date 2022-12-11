<?php

namespace App\Service;


use Symfony\Component\Routing\Annotation\Route;
use App\Entity\MediaObject;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;

class CreateMediaObjectAction  
{
   /* protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }*/
    public function getMediaObject(Request $request): MediaObject
    {
        //$request = $this->requestStack->getCurrentRequest();

        $uploadedFile = $request->files->get('image');

        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }
       
        //$mediaObject = new MediaObject();
        //$mediaObject->file = $uploadedFile;

        //var_dump($mediaObject->file->getRealPath()); //tmp
        //exit();

        return $mediaObject;
    }
}
