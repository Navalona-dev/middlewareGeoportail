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


use Doctrine\ORM\ORMInvalidArgumentException;
use App\Exception\PropertyVideException;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Exception\UnsufficientPrivilegeException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class RouteController extends AbstractController
{
    /**
     * @Route("/api/route/add", name="route_add", methods={"POST"})
     */
    public function create(Request $request, RouteService $routeService)
    {    
        $response = new Response();

        try {

            $data = [];
            $data['region' ] = $request->get('region');
            $data['district' ] = $request->get('district');
            $data['commune' ] = $request->get('commune');
            $data['localite' ] = $request->get('localite');
            $data['sourceInformation' ] = $request->get('sourceInformation');
            $data['pkDebut' ] = $request->get('pkDebut');
            $data['pkFin' ] = $request->get('pkFin');
            $data['largeurHausse' ] = $request->get('largeurHausse');
            $data['largeurAccotement' ] = $request->get('largeurAccotement');
            $data['modeAcquisitionInformation' ] = $request->get('modeAcquisitionInformation');
            $data['gestionnaire'] = $request->get('gestionnaire');
            $data['modeGestion'] = $request->get('modeGestion');
            $data['latitude'] = $request->get('latitudePKDebut');
            $data['longitude'] = $request->get('longitudePKDebut');
            $data['axe'] = $request->get('axe');
            $data['procedureTravaux'] = $request->get('procedureTravaux');
            $data['etat'] = $request->get('etat');
            $data['fonctionnel'] = $request->get('fonctionnel');
            $data['surfaceRevetement'] = $request->get('surfaceRevetement');
            $data['surfaceNidPoule'] = $request->get('surfaceNidPoule');
            $data['surfaceArrachement'] = $request->get('surfaceArrachement');
            $data['surfaceRessuage'] = $request->get('surfaceRessuage');
            $data['surfaceFissureJoint'] = $request->get('surfaceFissureJoint');
            $data['surfaceNonRevetuTraverse'] = $request->get('surfaceNonRevetuTraverse');
            $data['surfaceBourbier'] = $request->get('surfaceBourbier');
            $data['surfaceTeteChat'] = $request->get('surfaceTeteChat');
            $data['structureFissure'] = $request->get('structureFissure');
            $data['structureFaiencage'] = $request->get('structureFaiencage');
            $data['structureNidPouleStructure'] = $request->get('structureNidPouleStructure');
            $data['structureDeformation'] = $request->get('structureDeformation');
            $data['structureTeteOndule'] = $request->get('structureTeteOndule');
            $data['structureRavines'] = $request->get('structureRavines');
            $data['structureOrnierage'] = $request->get('structureOrnierage');
            $data['accotementHasAccotementGauche'] = $request->get('accotementHasAccotementGauche');
            $data['accotementGauche'] = $request->get('accotementGauche');
            $data['accotementDroite'] = $request->get('accotementDroite');
            $data['accotementIsAccotementNonRevetu'] = $request->get('accotementIsAccotementNonRevetu');
            $data['accotementHasAccotementRevetue'] = $request->get('accotementHasAccotementRevetue');
            $data['accotementTypeRevetementAccotement'] = $request->get('accotementTypeRevetementAccotement');
            $data['accotementDegrationSurface'] = $request->get('accotementDegrationSurface');
            $data['accotementDentelleRive'] = $request->get('accotementDentelleRive');
            $data['accotementDenivellationChausseAccotement'] = $request->get('accotementDenivellationChausseAccotement');
            $data['accotementDestructionAffouillementAccotement'] = $request->get('accotementDestructionAffouillementAccotement');
            $data['accotementNonRevetueDeformationProfil'] = $request->get('accotementNonRevetueDeformationProfil');
            $data['fosseRevetuDegradationFosse'] = $request->get('fosseRevetuDegradationFosse');
            $data['fosseRevetuSectionBouche'] = $request->get('fosseRevetuSectionBouche');
            $data['fosseNonRevetuFosseProfil'] = $request->get('fosseNonRevetuFosseProfil');
            $data['fosseNonRevetuEncombrement'] = $request->get('fosseNonRevetuEncombrement');
            
            $uploadedFile1 = $request->files->get('photo1');
            $uploadedFile2 = $request->files->get('photo2');
            $uploadedFile3 = $request->files->get('photo3');

            if (null != $uploadedFile1) {
                $nomOriginal1 = $uploadedFile1->getClientOriginalName();
                $tmpPathName1 = $uploadedFile1->getPathname();
                $directory1 = $this->getParameter('pathImageRoute') . "photo1/";
                $name_temp = hash('sha512', session_id().microtime($nomOriginal1));
                $nomPhoto1 = $name_temp.".".$uploadedFile1->getClientOriginalExtension();
                
                move_uploaded_file($tmpPathName1, $directory1.$nomPhoto1);
                $data['photo1'] = $this->getParameter('pathForNamePhotoRoute')."photo1/" .$nomPhoto1;
            }
            
            if (null != $uploadedFile2) {
                $nomOriginal2 = $uploadedFile2->getClientOriginalName();
                $tmpPathName2 = $uploadedFile2->getPathname();
                $directory2 = $this->getParameter('pathImageRoute') . "photo2/";
                $name_temp2 = hash('sha512', session_id().microtime($nomOriginal2));
                $nomPhoto2 = $name_temp2.".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName2, $directory2.$nomPhoto2);
                $data['photo2'] = $this->getParameter('pathForNamePhotoRoute')."photo2/" .$nomPhoto2;
            }

            if (null != $uploadedFile3) {
                $nomOriginal3 = $uploadedFile3->getClientOriginalName();
                $tmpPathName3 = $uploadedFile3->getPathname();
                $directory3 = $this->getParameter('pathImageRoute') . "photo3/";
                $name_temp3 = hash('sha512', session_id().microtime($nomOriginal3));
                $nomPhoto3 = $name_temp3.".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName3, $directory3.$nomPhoto3);
                $data['photo3'] = $this->getParameter('pathForNamePhotoRoute')."photo3/" .$nomPhoto3;
            }

            $idInfra = $routeService->addInfrastructureRoute($data);

            if ($idInfra != false) {
                // add situation et etat
                $idEtat = $routeService->addInfrastructureRouteEtat($idInfra, $data);

                $idSituation = $routeService->addInfrastructureRouteSituation($idInfra, $data);

                $idSurface = $routeService->addInfrastructureRouteSurface($idInfra, $data);

                $idStructure = $routeService->addInfrastructureRouteStructure($idInfra, $data);

                $idCollecteDonne = $routeService->addInfrastructureRouteCollecte($idInfra, $data);

                if ($idCollecteDonne != false) {
                    $idAccotement = $routeService->addInfrastructureRouteAccotement($idCollecteDonne, $data);

                    $idFosse = $routeService->addInfrastructureRouteFosse($idCollecteDonne, $data);
                }
                //$idDonneAnnexe = $routeService->addInfrastructureEducationDonneAnnexe($idInfra, $data);
            }

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route created_successfull"
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

    /**
     * @Route("/api/infra/route/liste", name="route_list", methods={"GET"})
     */
    public function listeRoute(Request $request, RouteService $routeService)
    {    
        $response = new Response();
        
        try {

            $routes = $routeService->getAllInfrastructuresRoute();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route list_successfull",
                'data' => $routes
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

    /**
     * @Route("/api/infra/route/base/liste", name="route_base_list", methods={"GET"})
     */
    public function listeBaseRoute(Request $request, RouteService $routeService)
    {   
        $response = new Response();
        
        try {

            $baseRoutes = $routeService->getAllInfrastructuresBaseRoute();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route base list_successfull",
                'data' => $baseRoutes
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

     /**
     * @Route("/api/route/base/add", name="route_base_add", methods={"POST"})
     */
    public function createBaseRoute(Request $request, RouteService $routeService)
    {    
        $response = new Response();
        
        try {

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
            
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route created_successfull"
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
