<?php

namespace App\Controller;

use App\Service\InfrastructureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class InfrastructuresController extends AbstractController
{
    /**
     * @Route("/infrastructures", name="app_infrastructures")
     */
    public function index(): Response
    {
        return $this->render('infrastructures/index.html.twig', [
            'controller_name' => 'InfrastructuresController',
        ]);
    }

    /**
     * @Route("/api/infrastructures/domaines", name="infrastructure_codes_list", methods={"GET"})
     */
    public function listeDomaineInfrastructure(Request $request, InfrastructureService $infrastructureService)
    {    
        $domaineInfrastructure = $infrastructureService->getAllDomainesInfrastructure();
        
        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "infrastructure code list_successfull",
            'data' => $domaineInfrastructure
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    /**
     * @Route("/api/infrastructures/niveau/{domaine}", name="infrastructure_codes_list", methods={"GET"})
     */
    public function listeNiveauInfrastructureByDomaine(Request $request, InfrastructureService $infrastructureService)
    {    
        $domaine = $request->query->get('domaine');
        $niveauInfrastructure = $infrastructureService->getAllNiveauInfrastructureByDomaine($domaine);
        
        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "infrastructure code list_successfull",
            'data' => $niveauInfrastructure
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
