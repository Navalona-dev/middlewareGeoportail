<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\EducationRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Service\CreateMediaObjectAction;

class EducationController extends AbstractController
{
    /**
     * @Route("/api/education/add", name="education_add", methods={"POST"})
     */
    public function create(Request $request, EducationRepository $educationRepository)
    {    
       
        $data = json_decode($request->getContent(), true);
        
        $infrastructures = $educationRepository->addEducation($data['nom'], $data['indicatif'], $data['categorie'], $data['localite'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['communeTerrain'], $data['numeroSequence'], (int) $data['codeProduit'], (int) $data['codeCommune'], (float) $data['latitude'],(float) $data['longitude']);
        

        //var_dump($infrastructures);
       /* $uploadedFile = $request->files->get('image');
        var_dump($uploadedFile);*/
        
        //$mediaObjectAction = $this->get('app.media');
       
       /*$mediaObject = $mediaObjectAction->getMediaObject($request);
      var_dump($mediaObject->getRealPath());
       die($mediaObject->getpathName());*/

       //var_dump($data);
       //exit();
        //$product = $this->get('serializer')->deserialize($data, 'App\Entity\Product', 'json');
       
        //$em = $this->getDoctrine()->getManager();
        //$em->persist($product);
        //$em->flush();

        $response = new Response();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        /*$products = $this->getDoctrine()
        ->getRepository(Product::class)
        ->findAll();*/
        /*if (!$products) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }*/

        //$jsonContent = $serializer->serialize($products, 'json');

        $jsonContent = [];

         /**
          * fin serialisation
          */

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "education created_successfull"
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    /**
     * @Route("/api/infra/education/liste", name="education_list", methods={"GET"})
     */
    public function listeEducation(Request $request, EducationRepository $educationRepository)
    {    
        $infrastructures = $educationRepository->getAllInfrastructuresEducation();

        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "education list_successfull",
            'data' => $infrastructures
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
