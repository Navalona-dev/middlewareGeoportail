<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\LocalisationInfrastructureService;
use Symfony\Component\HttpFoundation\Request;

class LocalisationInfrastructureController extends AbstractController
{
    /**
     * @Route("/api/regions", name="all_region", methods={"GET"})
     */
    public function listeRegionsInfrastructure(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        $regionsInfrastructure = $localisationInfrastructureService->getAllRegions();
        
        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "infrastructure regions list_successfull",
            'data' => $regionsInfrastructure
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

     /**
     * @Route("/api/communes/{region}", name="commmunes_region", methods={"GET"})
     */
    public function listeCommunesByRegion(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        $region = $request->query->get('region');
        $commmunesInfrastructure = $localisationInfrastructureService->getAllCommunesByRegion($region);
        
        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "infrastructure code list_successfull",
            'data' => $commmunesInfrastructure
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
