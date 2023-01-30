<?php

namespace App\Controller;

use App\Service\InfrastructureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\ORMInvalidArgumentException;
use App\Exception\PropertyVideException;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Exception\UnsufficientPrivilegeException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

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
     * @Route("/api/infrastructures/niveau", name="infrastructure_codes_list_domaine", methods={"POST"})
     */
    public function listeNiveauInfrastructureByDomaineNiveau3(Request $request, InfrastructureService $infrastructureService)
    {    
        $data = json_decode($request->getContent(), true);

        $response = new Response();

        try {

            switch ($data["type"]) {
                case 'education':
                    $niveauInfrastructure = $infrastructureService->getAllNiveauInfrastructureByDomaineNiveau3($data["domaine"]);
                    break;
                case 'sante':
                    # code...
                    break;
                default:
                    # code...
                    break;
            }

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "infrastructure niveau list_successfull",
                'data' => $niveauInfrastructure
            ]));

            $response->headers->set('Content-Type', 'application/json');

        } catch (PropertyVideException $PropertyVideException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $PropertyVideException->getMessage()
            ]));
        } catch (UniqueConstraintViolationException $UniqueConstraintViolationException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $UniqueConstraintViolationException->getMessage()
            ]));
        } catch (MappingException $MappingException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $MappingException->getMessage()
            ]));
        } catch (ORMInvalidArgumentException $ORMInvalidArgumentException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $ORMInvalidArgumentException->getMessage()
            ]));
        } catch (UnsufficientPrivilegeException $UnsufficientPrivilegeException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $UnsufficientPrivilegeException->getMessage(),
            ]));
        /*} catch (ServerException $ServerException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $ServerException->getMessage(),
            ]));*/
        } catch (NotNullConstraintViolationException $NotNullConstraintViolationException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $NotNullConstraintViolationException->getMessage(),
            ]));
        } catch (\Exception $Exception) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $Exception->getMessage(),
            ]));
        }
        return $response;
    }
}
