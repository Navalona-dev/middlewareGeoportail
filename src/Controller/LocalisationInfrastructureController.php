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
     * @Route("/api/regions", name="all_regions", methods={"GET"})
     */
    public function listeRegionsInfrastructure(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        $regionsInfrastructure = $localisationInfrastructureService->getAllRegions();
        
        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "Regions list_successfull",
            'data' => $regionsInfrastructure
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

     /**
     * @Route("/api/districts", name="districts_region", methods={"POST"})
     */
    public function listeDistrictsByRegion(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        $data = json_decode($request->getContent(), true);
        $region = $data['region'];
        $commmunesInfrastructure = $localisationInfrastructureService->getAllDistrictByRegion($region);
        
        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "Districts list_successfull",
            'data' => $commmunesInfrastructure
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    /**
     * @Route("/api/communes", name="commmunes_district_region", methods={"POST"})
     */
    public function listeCommunesByDistrictInRegion(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        $data = json_decode($request->getContent(), true);
        $region = $data['region'];
        $district = $data['district'];
        $commmunesInfrastructure = $localisationInfrastructureService->getAllCommunesByDistrictInRegion($region, $district);
        
        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "communes list_successfull",
            'data' => $commmunesInfrastructure
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
