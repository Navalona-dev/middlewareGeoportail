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
use App\Service\PontService;


use Doctrine\ORM\ORMInvalidArgumentException;
use App\Exception\PropertyVideException;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Exception\UnsufficientPrivilegeException;
use App\Service\TrajetrouteService;
use DateTime;
use Symfony\Component\HttpClient\Exception\ServerException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class TrajetrouteController extends AbstractController
{
    private $pathImage = null;
    private $pathImageTrajetroute = null;
    private $pathPublic = null;
    private $pathForNamePhotoTrajetroute = null;
    private $kernelInterface;
    private $directoryCopy = null;

    public function __construct(ParameterBagInterface $params, KernelInterface  $kernelInterface) {
        $this->pathImage = $params->get('base_url'). $params->get('pathPublic') . "trajetroute/";
        $this->pathImageTrajetroute = $params->get('pathImageTrajetroute');
        $this->pathPublic = $params->get('pathPublic');
        $this->pathForNamePhotoTrajetroute = $params->get('pathForNamePhotoTrajetroute');
        $this->kernelInterface = $kernelInterface;
        $this->directoryCopy= $kernelInterface->getProjectDir()."/public".$params->get('pathPublic')."trajetroute/";
    }


    /**
     * @Route("/api/trajetroute/addphoto", name="trajetroute_add_photo", methods={"POST"})
     */
    public function addPhoto(Request $request, TrajetrouteService $trajetrouteService)
    { 
        $response = new Response();
        $hasException = false;
        $idInfra = null;
        try {
            $data = [];
            $uploadedFile1 = $request->files->get('photo1');
            $uploadedFile2 = $request->files->get('photo2');
            $uploadedFile3 = $request->files->get('photo3');
            $idInfra = $request->get('infraId');
            $data['photo1'] = null;
            $data['photo2'] = null;
            $data['photo3'] = null;
            $data['photoName1'] = null;
            $data['photoName2'] = null;
            $data['photoName3'] = null;
            if (null != $uploadedFile1) {
                $nomOriginal1 = $uploadedFile1->getClientOriginalName();
                $tmpPathName1 = $uploadedFile1->getPathname();
                $directory1 = $this->pathImageTrajetroute . "photo1/";
                $directoryPublicCopy =  $this->directoryCopy. "photo1/";

                $name_temp = hash('sha512', session_id().microtime($nomOriginal1));
                $nomPhoto1 = $name_temp.".".$uploadedFile1->getClientOriginalExtension();
                
                move_uploaded_file($tmpPathName1, $directory1.$nomPhoto1);
                copy($directory1.$nomPhoto1, $directoryPublicCopy.$nomPhoto1);

                $data['photo1'] = $this->pathForNamePhotoTrajetroute."photo1/" .$nomPhoto1;
                $data['photoName1'] = $nomPhoto1;
            }
            
            if (null != $uploadedFile2) {
                $nomOriginal2 = $uploadedFile2->getClientOriginalName();
                $tmpPathName2 = $uploadedFile2->getPathname();
                $directory2 = $this->pathImageTrajetroute . "photo2/";
                $directoryPublicCopy =  $this->directoryCopy. "photo2/";

                $name_temp2 = hash('sha512', session_id().microtime($nomOriginal2));
                $nomPhoto2 = $name_temp2.".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName2, $directory2.$nomPhoto2);
                copy($directory2.$nomPhoto2, $directoryPublicCopy.$nomPhoto2);
                
                $data['photo2'] = $this->pathForNamePhotoTrajetroute."photo2/" .$nomPhoto2;
                $data['photoName2'] = $nomPhoto2;
            }

            if (null != $uploadedFile3) {
                $nomOriginal3 = $uploadedFile3->getClientOriginalName();
                $tmpPathName3 = $uploadedFile3->getPathname();
                $directory3 = $this->pathImageTrajetroute . "photo3/";
                $directoryPublicCopy =  $this->directoryCopy. "photo3/";

                $name_temp3 = hash('sha512', session_id().microtime($nomOriginal3));
                $nomPhoto3 = $name_temp3.".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName3, $directory3.$nomPhoto3);
                copy($directory3.$nomPhoto3, $directoryPublicCopy.$nomPhoto3);

                $data['photo3'] = $this->pathForNamePhotoTrajetroute."photo3/" .$nomPhoto3;
                $data['photoName3'] = $nomPhoto3;
            }

            $idInfra = $trajetrouteService->addInfrastructurePhoto($idInfra, $data);

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Photo trajet route created_successfull"
            ]));

            $response->headers->set('Content-Type', 'application/json');

        } catch (PropertyVideException $PropertyVideException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $PropertyVideException->getMessage()
            ]));
        } catch (UniqueConstraintViolationException $UniqueConstraintViolationException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $UniqueConstraintViolationException->getMessage()
            ]));
        } catch (MappingException $MappingException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $MappingException->getMessage()
            ]));
        } catch (ORMInvalidArgumentException $ORMInvalidArgumentException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $ORMInvalidArgumentException->getMessage()
            ]));
        } catch (UnsufficientPrivilegeException $UnsufficientPrivilegeException) {
            $hasException = true;
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
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $NotNullConstraintViolationException->getMessage(),
            ]));
        } catch (\Exception $Exception) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $Exception->getMessage(),
            ]));
        }

        if ($hasException) {// Clean database
            /*$trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'situation');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'data');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'etude');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');*/
            /*
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'surface');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'structure');
            
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'accotement');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'fosse');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
        
            */
        
        }
        
        return $response;
    }

    /**
     * @Route("/api/trajetroute/add", name="trajetroute_add", methods={"POST"})
     */
    public function create(Request $request, TrajetrouteService $trajetrouteService)
    {    
        $response = new Response();
        $hasException = false;
        $idInfra = null;
        try {
            $infos = json_decode($request->getContent(), true);
            $data = [];
            $data['nomRouteRattache' ] = $infos['nomRouteRattache'];
            $data['localiteDepart' ] = $infos['localiteDepart'];
            $data['localiteArrive' ] = $infos['localiteArrive'];
            $data['nom' ] = $infos['nom'];
            $data['pkDepart' ] = $infos['pkDepart'];
            $data['pkArrive' ] = $infos['pkArrive'];
            $data['categorie' ] = $infos['categorie'];
            dd($data, $infos);
            $multipleCoordonne['coordonnees'] = "";
            if (count($infos['localisations']) > 0) {
                
                foreach ($infos['localisations'] as $key => $value) {
                    if (count($infos['localisations']) - 1 == $key) {
                        $multipleCoordonne .= $value['latitude']." ".$value['longitude'];
                    } else {
                        $multipleCoordonne .= $value['latitude']." ".$value['longitude'].", ";
                    }
                    
                }
            }

            $idInfra = $trajetrouteService->addInfrastructure($data);

            if ($idInfra != false) {
                // add situation et etat
               
                 /**
                 * Administrative data
                 */

                 // Data collecte
                 if (null != $infos['data'] && count($infos['data']) > 0) {
                    $data['praticableAnnee'] = $infos['data']['praticableAnnee'];
                    $data['moisOuverture'] = $infos['data']['moisOuverture'];
                    $data['moisFermeture'] = $infos['data']['moisFermeture'];
                    $data['dureeTrajetSaisonSeche'] = $infos['data']['dureeTrajetSaisonSeche'];
                    $data['sourceInformationData'] = $infos['data']['sourceInformationData'];
                    $data['modeAcquisitionInformationData'] = $infos['data']['modeAcquisitionInformationData'];
                    $data['revetementData'] = $infos['data']['revetementData'];
                    $data['dateInformationData'] = $infos['data']['dateInformationData'];
                    $idDataCollected = $trajetrouteService->addInfrastructureDonneCollecte($idInfra, $data);

                 }
            

                //Situation
                if (null != $infos['situation'] && count($infos['situation']) > 0) {
                    // Situation
                    $data['fonctionnel'] = $infos['situation']['fonctionnel'];
                    $data['motif'] = $infos['situation']['raison'];
                    $data['sourceInformationSituation'] = $infos['situation']['sourceInformationSituation'];
                    $data['modeAcquisitionInformationSituation'] = $infos['situation']['modeAcquisitionInformationSituation'];
                    $data['etat'] = $infos['situation']['etat'];
                    $data['raisonPrecision'] = $infos['situation']['raisonPrecision'];
                    $idSituation = $trajetrouteService->addInfrastructureSituation($idInfra, $data);

                }

                if (null != $infos['etat'] && count($infos['etat']) > 0) {
                    // Etat
                    $data['etat'] = $infos['etat']['etat'];
                    $data['sourceInformationEtat'] = $infos['etat']['sourceInformationEtat'];
                    $data['modeAcquisitionInformationEtat'] = $infos['etat']['modeAcquisitionInformationEtat'];
            
                    $idEtat = $trajetrouteService->addInfrastructureRouteEtat($idInfra, $data);

                }
                //Travaux 
                if (null != $infos['travaux'] && count($infos['travaux']) > 0) {
                    $data['consistanceTravaux'] = $infos['travaux']['consistanceTravaux'];
                    $data['objetTravaux'] = $infos['travaux']['objetTravaux'];
                    $data['modeRealisationTravaux'] = $infos['travaux']['modeRealisationTravaux'];
                    $data['maitreOuvrageTravaux'] = $infos['travaux']['maitreOuvrageTravaux'];
                    $data['maitreOuvrageDelegueTravaux'] = $infos['travaux']['maitreOuvrageDelegueTravaux'];
                    $data['maitreOeuvreTravaux'] = $infos['travaux']['maitreOeuvreTravaux'];
                    $data['idControleSurveillanceTravaux'] = $infos['travaux']['idControleSurveillanceTravaux'];
                    $data['modePassationTravaux'] = $infos['travaux']['modePassationTravaux'];
                    $data['porteAppelOffreTravaux'] = $infos['travaux']['porteAppelOffreTravaux'];
                    $data['montantTravaux'] = $infos['travaux']['montantTravaux'];
                    $data['numeroContratTravaux'] = $infos['travaux']['numeroContratTravaux'];
                    $data['dateContratTravaux'] = $infos['travaux']['dateContratTravaux'];
                    $data['dateOrdreServiceTravaux'] = $infos['travaux']['dateOrdreServiceTravaux'];
                    $data['idTitulaireTravaux'] = $infos['travaux']['idTitulaireTravaux'];
                    $data['resultatTravaux'] = $infos['travaux']['resultatTravaux'];
                    $data['motifRuptureContratTravaux'] = $infos['travaux']['motifRuptureContratTravaux'];
                    $data['dateReceptionProvisoireTravaux'] = $infos['travaux']['dateReceptionProvisoireTravaux'];
                    $data['dateReceptionDefinitiveTravaux'] = $infos['travaux']['dateReceptionDefinitiveTravaux'];
                    $data['ingenieurReceptionProvisoireTravaux'] = $infos['travaux']['ingenieurReceptionProvisoireTravaux'];
                    $data['ingenieurReceptionDefinitiveTravaux'] = $infos['travaux']['ingenieurReceptionDefinitiveTravaux'];
                    $data['dateInformationTravaux'] = $infos['travaux']['dateInformationTravaux'];
                    $data['sourceInformationTravaux'] = $infos['travaux']['sourceInformationTravaux'];
                    $data['modeAcquisitionInformationTravaux'] = $infos['travaux']['modeAcquisitionInformationTravaux'];
                    $data['bailleurTravaux'] = $infos['travaux']['bailleurTravaux'];
                    $data['precisionConsitanceTravaux'] = $infos['travaux']['precisionConsitanceTravaux'];
                    $data['precisionPassationTravaux'] = $infos['travaux']['precisionPassationTravaux'];


                    $idTravaux = $trajetrouteService->addInfrastructureTravaux($idInfra, $data);
                }
                
                // Fournitures
                if (null != $infos['etude'] && count($infos['etude']) > 0) {
                    // Fourniture
                    $data['objetContratFourniture'] = $infos['fourniture']['objetContratFourniture'];
                    $data['consistanceContratFourniture'] = $infos['fourniture']['consistanceContratFourniture'];
                    $data['entiteFourniture'] = $infos['fourniture']['entiteFourniture'];
                    $data['modePassationFourniture'] = $infos['fourniture']['modePassationFourniture'];
                    $data['porteAppelOffreFourniture'] = $infos['fourniture']['porteAppelOffreFourniture'];
                    $data['montantFourniture'] = $infos['fourniture']['montantFourniture'];
                    $data['idTitulaireFourniture'] = $infos['fourniture']['idTitulaireFourniture'];
                    $data['numeroContratFourniture'] = $infos['fourniture']['numeroContratFourniture'];
                    $data['dateContratFourniture'] = $infos['fourniture']['dateContratFourniture'];
                    $data['dateOrdreFourniture'] = $infos['fourniture']['dateOrdreFourniture'];
                    $data['resultatFourniture'] = $infos['fourniture']['resultatFourniture'];
                    $data['raisonResiliationFourniture'] = $infos['fourniture']['raisonResiliationFourniture'];
                    $data['bailleurFourniture'] = $infos['fourniture']['bailleurFourniture'];
                    $data['precisionConsitanceFourniture'] = $infos['fourniture']['precisionConsitanceFourniture'];
                    $data['precisionPassationFourniture'] = $infos['fourniture']['precisionPassationFourniture'];
                    $idFourniture = $trajetrouteService->addInfrastructureRouteFourniture($idInfra, $data);
                }
                
                
                // Etudes
                if (null != $infos['etude'] && count($infos['etude']) > 0) {
                    // Etude
                    $data['objetContratEtude'] = $infos['etude']['objetContratEtude'];
                    $data['consistanceContratEtude'] = $infos['etude']['consistanceContratEtude'];
                    $data['entiteEtude'] = $infos['etude']['entiteEtude'];
                    $data['idTitulaireEtude'] = $infos['etude']['idTitulaireEtude'];
                    $data['montantContratEtude'] = $infos['etude']['montantContratEtude'];
                    $data['numeroContratEtude'] = $infos['etude']['numeroContratEtude'];
                    $data['modePassationEtude'] = $infos['etude']['modePassationEtude'];
                    $data['porteAppelOffreEtude'] = $infos['etude']['porteAppelOffreEtude'];
                    $data['dateOrdreServiceEtude'] = $infos['etude']['dateOrdreServiceEtude'];
                    $data['resultatPrestationEtude'] = $infos['etude']['resultatPrestationEtude'];
                    $data['bailleurEtude'] = $infos['etude']['bailleurEtude'];
                    $data['precisionConsitanceEtude'] = $infos['etude']['precisionConsitanceEtude'];
                    $data['precisionPassationEtude'] = $infos['etude']['precisionPassationEtude'];

                    $idEtude = $trajetrouteService->addInfrastructureEtudes($idInfra, $data);
                }
                
                /**
                 * End Administrative data
                */
                //$idDonneAnnexe = $trajetrouteService->addInfrastructureEducationDonneAnnexe($idInfra, $data);
            }

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Trajet route created_successfull"
            ]));

            $response->headers->set('Content-Type', 'application/json');

        } catch (PropertyVideException $PropertyVideException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $PropertyVideException->getMessage()
            ]));
        } catch (UniqueConstraintViolationException $UniqueConstraintViolationException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $UniqueConstraintViolationException->getMessage()
            ]));
        } catch (MappingException $MappingException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $MappingException->getMessage()
            ]));
        } catch (ORMInvalidArgumentException $ORMInvalidArgumentException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $ORMInvalidArgumentException->getMessage()
            ]));
        } catch (UnsufficientPrivilegeException $UnsufficientPrivilegeException) {
            $hasException = true;
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
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $NotNullConstraintViolationException->getMessage(),
            ]));
        } catch (\Exception $Exception) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $Exception->getMessage(),
            ]));
        }

        if ($hasException) {// Clean database
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'situation');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'data');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'etude');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');
            /*
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'surface');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'structure');
            
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'accotement');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'fosse');
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
           
            $trajetrouteService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');*/
           
        }
        
        return $response;
    }

    /**
     * @Route("/api/infra/trajetroute/liste", name="trajetroute_list", methods={"GET"})
     */
    public function listeTrajeRoute(Request $request, TrajetrouteService $trajetrouteService)
    {    
        $response = new Response();
        
        try {

            $routes = $trajetrouteService->getAllInfrastructures();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Trajet route list_successfull",
                'pathImage' => $this->pathImage,
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
     * @Route("/api/infra/trajetroute/liste/minifie", name="trajetroute_list_minifie", methods={"GET"})
     */
    public function listeTrajetrouteMinifie(Request $request, TrajetrouteService $trajetrouteService)
    {    
        $response = new Response();
        
        try {

            $routes = $trajetrouteService->getAllInfrastructuresMinifie();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Trajet route list_successfull",
                'pathImage' => $this->pathImage,
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
     * @Route("/api/infra/trajetroute/info", name="trajetroute_info", methods={"POST"})
     */
    public function getOneInfraInfo(Request $request, TrajetrouteService $trajetrouteService)
    {    
        $response = new Response();
        
        try {
            $infraId = $request->get('id');

            $routes = $trajetrouteService->getOneInfraInfo(intval($infraId));

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Trajet route infrastructure successfull",
                'pathImage' => $this->pathImage,
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
     * @Route("/api/trajetroute/update", name="trajetroute_update", methods={"POST"})
     */
    public function update(Request $request, TrajetrouteService $trajetrouteService)
    {    
        $response = new Response();
        $hasException = false;
        $idInfra = null;
        try {
            $data = $request->getContent();
            $data = array();
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                // Infrastructure
                $hasInfraChanged = false;
                $updateColonneInfra = "";
                $idInfra = 0;
                
                $colonneInteger = ['id', 'gid', 'id_infrastructure', 'id_controle_surveillance', 'montant', 'id_titulaire', 'id_ingenieurs_reception_provisoire',
                'id_ingenieurs_reception_definitive', 'montant_contrat', 'nombre_voies', 'pk_debut', 'pk_fin', 'capacite_de_voiture_accueillies', 'pk_depart', 'pk_arrive'];
                $colonneFloat = ['longueur', 'largeur', 'charge_maximum', 'Largeur_chaussée', 'Largeur_accotements', 'decalage_de_la_jointure_du_tablier_chaussee_en_affaissement', 'decalage_de_la_jointure_du_tablier_chaussee_en_ecartement'];

                if (array_key_exists('infrastructure', $data) && count($data['infrastructure']) > 0) {
                    $hasInfraChanged = true;
                    $i = 0;
                    foreach ($data['infrastructure'] as $colonne => $value) {
                        if (in_array($colonne, $colonneInteger)) {
                            $value = intval($value);
                            if ($colonne == "id" || $colonne == "gid") {
                                $idInfra = $value;
                            }

                        } elseif(in_array($colonne, $colonneFloat)) {  
                            $value = floatval($value);
                        } else {
                            $value = "'$value'";
                        }

                        if ($colonne != "id" && $colonne != "gid") {
                            if (count($data['infrastructure']) - 1 != $i) {
                                $updateColonneInfra .= $colonne."="."$value".", ";
                            } else {
                                $updateColonneInfra .= $colonne."="."$value";
                            }
                        } 
                        $i++;
                    }
                    $idInfra = $trajetrouteService->updateInfrastructure($idInfra, $updateColonneInfra);
                }
                // Situation
                $hasEtatChanged = false;
                $updateColonneEtat = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idSituation = 0;
                if (array_key_exists('situations', $data) && count($data['situations']) > 0) {
                    $hasEtatChanged = true;
                    $i = 0;
                    foreach ($data['situations'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idSituation = intval($value);
                        }
                        
                        if (in_array($colonne, $colonneInteger)) {
                            $value = intval($value);
                        } elseif (in_array($colonne, $colonneFloat)) {  
                            $value = floatval($value);
                        } elseif ($colonne == "date_information") {
                            $date = new \DateTime($value);
                            $value = $date->format('Y-m-d H:i:s');
                            $value = "'$value'";
                        } elseif ($colonne == "source_information") {
                            $value = pg_escape_string($value);
                            $value = "'$value'";
                        } else {
                            $value = "'$value'";
                        }

                        if ($colonne != "id" && $colonne != "gid") {
                            if (count($data['etat']) - 1 != $i) {
                                $updateColonneEtat .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                            } else {
                                $updateColonneEtat .= $colonne."="."$value";
                                $colonneInsert .= $colonne;
                                $valuesInsert .= $value;
                            }
                        } 
                        $i++;
                    }
                    if ($idSituation == 0) {
                        $idSituation = $trajetrouteService->addInfoInTableByInfrastructure('t_pnr_02_situation', $colonneInsert, $valuesInsert);
                    } else {
                        $idSituation = $trajetrouteService->updateInfrastructureTables('t_pnr_02_situation', $idSituation, $updateColonneEtat);
                    } 
                    
                }

                // Data collecte
                $hasDataChanged = false;
                $updateColonneData = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idData = 0;
                if (array_key_exists('data_collecte', $data) && count($data['data_collecte']) > 0) {
                    $hasDataChanged = true;
                    $i = 0;
                    foreach ($data['data_collecte'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idData = intval($value);
                        }
                        
                        if (in_array($colonne, $colonneInteger)) {
                            $value = intval($value);
                        } elseif (in_array($colonne, $colonneFloat)) {  
                            $value = floatval($value);
                        } elseif ($colonne == "date_information") {
                            $date = new \DateTime($value);
                            $value = $date->format('Y-m-d H:i:s');
                            $value = "'$value'";
                        } elseif ($colonne == "source_information") {
                            $value = pg_escape_string($value);
                            $value = "'$value'";
                        } else {
                            $value = "'$value'";
                        }

                        if ($colonne != "id" && $colonne != "gid") {
                            if (count($data['data_collecte']) - 1 != $i) {
                                $updateColonneData .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                            } else {
                                $updateColonneData .= $colonne."="."$value";
                                $colonneInsert .= $colonne;
                                $valuesInsert .= $value;
                            }
                            
                        } 
                        $i++;
                    }

                    if ($idData == 0) {
                        $idData = $trajetrouteService->addInfoInTableByInfrastructure('t_pnr_04_donnees_collectees', $colonneInsert, $valuesInsert);
                    } else {
                        $idData = $trajetrouteService->updateInfrastructureTables('t_pnr_04_donnees_collectees', $idData, $updateColonneData);
                    }
                }
                // Travaux
                $hasTravauxChanged = false;
                $updateColonneTravaux = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idTravaux = 0;
                if (array_key_exists('travaux', $data) && count($data['travaux']) > 0) {
                    $hasTravauxChanged = true;
                    $i = 0;
                    foreach ($data['travaux'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idTravaux = intval($value);
                        }
                        
                        if (in_array($colonne, $colonneInteger)) {
                            $value = intval($value);
                        } elseif (in_array($colonne, $colonneFloat)) {  
                            $value = floatval($value);
                        } elseif ($colonne == "date_information") {
                            $date = new \DateTime($value);
                            $value = $date->format('Y-m-d H:i:s');
                            $value = "'$value'";
                        } elseif ($colonne == "source_information") {
                            $value = pg_escape_string($value);
                            $value = "'$value'";
                        } else {
                            $value = "'$value'";
                        }

                        if ($colonne != "id" && $colonne != "gid") {
                            if (count($data['travaux']) - 1 != $i) {
                                $updateColonneTravaux .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                            } else {
                                $updateColonneTravaux .= $colonne."="."$value";
                                $colonneInsert .= $colonne;
                                $valuesInsert .= $value;
                            }
                            
                        } 
                        $i++;
                    }

                    if ($idTravaux == 0) {
                        $idTravaux = $trajetrouteService->addInfoInTableByInfrastructure('t_pnr_05_travaux', $colonneInsert, $valuesInsert);
                    } else {
                        $idTravaux = $trajetrouteService->updateInfrastructureTables('t_pnr_05_travaux', $idTravaux, $updateColonneTravaux);
                    }
                }

                // Etudes
                $hasEtudeChanged = false;
                $updateColonneEtudes = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idEtudes = 0;
                if (array_key_exists('etudes', $data) && count($data['etudes']) > 0) {
                    $hasEtudeChanged = true;
                    $i = 0;
                    foreach ($data['etudes'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idEtudes = intval($value);
                        }
                        
                        if (in_array($colonne, $colonneInteger)) {
                            $value = intval($value);
                        } elseif (in_array($colonne, $colonneFloat)) {  
                            $value = floatval($value);
                        } elseif ($colonne == "date_information") {
                            $date = new \DateTime($value);
                            $value = $date->format('Y-m-d H:i:s');
                            $value = "'$value'";
                        } elseif ($colonne == "source_information") {
                            $value = pg_escape_string($value);
                            $value = "'$value'";
                        } else {
                            $value = "'$value'";
                        }

                        if ($colonne != "id" && $colonne != "gid") {
                            if (count($data['etudes']) - 1 != $i) {
                                $updateColonneEtudes .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                            } else {
                                $updateColonneEtudes .= $colonne."="."$value";
                                $colonneInsert .= $colonne;
                                $valuesInsert .= $value;
                            }
                            
                        } 
                        $i++;
                    }

                    if ($idEtudes == 0) {
                        $idEtudes = $trajetrouteService->addInfoInTableByInfrastructure('t_pnr_07_etudes', $colonneInsert, $valuesInsert);
                    } else {
                        $idEtudes = $trajetrouteService->updateInfrastructureTables('t_pnr_07_etudes', $idEtudes, $updateColonneEtudes);
                    }
                }
            }
        
        
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Pont update_successfull"
            ]));

            $response->headers->set('Content-Type', 'application/json');

        } catch (PropertyVideException $PropertyVideException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $PropertyVideException->getMessage()
            ]));
        } catch (UniqueConstraintViolationException $UniqueConstraintViolationException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $UniqueConstraintViolationException->getMessage()
            ]));
        } catch (MappingException $MappingException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $MappingException->getMessage()
            ]));
        } catch (ORMInvalidArgumentException $ORMInvalidArgumentException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $ORMInvalidArgumentException->getMessage()
            ]));
        } catch (UnsufficientPrivilegeException $UnsufficientPrivilegeException) {
            $hasException = true;
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
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $NotNullConstraintViolationException->getMessage(),
            ]));
        } catch (\Exception $Exception) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $Exception->getMessage(),
            ]));
        }

        if ($hasException) {// Clean database
            //$dalotService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            //$dalotService->cleanTablesByIdInfrastructure($idInfra, 'etat');
            //$dalotService->cleanTablesByIdInfrastructure($idInfra, 'data');
            //$dalotService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            //$dalotService->cleanTablesByIdInfrastructure($idInfra, 'etude');
            /*
            $dalotService->cleanTablesByIdInfrastructure($idInfra, 'surface');
            $dalotService->cleanTablesByIdInfrastructure($idInfra, 'structure');
            
            $dalotService->cleanTablesByIdInfrastructure($idInfra, 'accotement');
            $dalotService->cleanTablesByIdInfrastructure($idInfra, 'fosse');
            $dalotService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
           
            $dalotService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');*/
           
        }
        
        return $response;
    }
}
