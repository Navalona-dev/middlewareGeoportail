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
use App\Service\RouteService;

class RouteController extends AbstractController
{
    /**
     * @Route("/api/route/add", name="route_add", methods={"POST"})
     */
    public function create(Request $request, RouteService $routeService)
    {    
       
        $data = json_decode($request->getContent(), true);
       
        $mulitpleCoordonne = "";
        /*if (count($data['localisation']) > 0) {
            
            foreach ($data['localisation'] as $key => $value) {
                if (count($data['localisation']) - 1 == $key) {
                    $mulitpleCoordonne .= $value['latitude']." ".$value['longitude'];
                } else {
                    $mulitpleCoordonne .= $value['latitude']." ".$value['longitude'].", ";
                }
                
            }
        }*/
        
        $result = $routeService->addInfrastructureRoute($data['categorie'], $data['localite'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['communeTerrain'], $data['pk']['debut'], $data['section'], $data['numeroRoute'], $data['gestionnaire'], $data['modeGestion'], null,  $data['pk']['fin'], null, $data['largeur']['hausse'], $data['largeur']['accotement'], $data['structure'], $data['region'], $data['district'], $data['gps']);
        

        $response = new Response();

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
    public function listeRoute(Request $request, RouteService $routeService)
    {    
        $routes = $routeService->getAllInfrastructuresRoute();

        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "route list_successfull",
            'data' => $routes
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    /**
     * @Route("/api/infra/route/base/liste", name="route_base_list", methods={"GET"})
     */
    public function listeBaseRoute(Request $request, RouteService $routeService)
    {    
        $baseRoutes = $routeService->getAllInfrastructuresBaseRoute();

        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "route base list_successfull",
            'data' => $baseRoutes
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

     /**
     * @Route("/api/route/base/add", name="route_add", methods={"POST"})
     */
    public function createBaseRoute(Request $request, RouteService $routeService)
    {    
       
        $data = json_decode($request->getContent(), true);
       
        $multipleCoordonne = "";
        if (count($data['localisation']) > 0) {
            
            foreach ($data['localisation'] as $key => $value) {
                if (count($data['localisation']) - 1 == $key) {
                    $multipleCoordonne .= $value['latitude']." ".$value['longitude'];
                } else {
                    $multipleCoordonne .= $value['latitude']." ".$value['longitude'].", ";
                }
                
            }
        }
        
        $result = $routeService->addInfrastructureBaseRoute($multipleCoordonne, $data['nom']);
        

        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "route created_successfull"
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
