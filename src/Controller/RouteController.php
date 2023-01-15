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
use App\Repository\RouteRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Service\CreateMediaObjectAction;

class RouteController extends AbstractController
{
    /**
     * @Route("/api/route/add", name="route_add", methods={"POST"})
     */
    public function create(Request $request, RouteRepository $RouteRepository)
    {    
       
        $data = json_decode($request->getContent(), true);
       
        $mulitpleCoordonne = "";
        if (count($data['localisation']) > 0) {
            
            foreach ($data['localisation'] as $key => $value) {
                if (count($data['localisation']) - 1 == $key) {
                    $mulitpleCoordonne .= $value['latitude']." ".$value['longitude'];
                } else {
                    $mulitpleCoordonne .= $value['latitude']." ".$value['longitude'].", ";
                }
                
            }
        }
        
        $RouteRepository->addInfrastructureRoute($data['categorie'], $data['localite'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['communeTerrain'], $mulitpleCoordonne, $data['pk']['debut'], $data['section'], $data['numeroRoute'], $data['gestionnaire'], $data['modeGestion'], null,  $data['pk']['fin'], null, $data['largeur']['hausse'], $data['largeur']['accotement'], $data['structure'], $data['region'], $data['district'], $data['gps']);
        

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
            'message' => "route created_successfull"
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    /**
     * @Route("/api/infra/route/liste", name="route_list", methods={"GET"})
     */
    public function listeRoute(Request $request, RouteRepository $RouteRepository)
    {    
        $infrastructures = $RouteRepository->getAllInfrastructuresRoute();

        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "route list_successfull",
            'data' => $infrastructures
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
