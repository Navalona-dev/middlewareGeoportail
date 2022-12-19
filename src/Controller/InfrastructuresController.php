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
     * @Route("/api/infrastructures/codes", name="infrastructure_codes_list", methods={"GET"})
     */
    public function listeInfrastructureCodes(Request $request, InfrastructureService $infrastructureService)
    {    
        $infrastructureCodes = $infrastructureService->getAllInfrastructureCodes();

        var_dump($infrastructureCodes);
        exit();
        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "infrastructure code list_successfull",
            'data' => $infrastructureCodes
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
