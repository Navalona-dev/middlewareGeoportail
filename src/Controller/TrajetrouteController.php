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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    private $urlGenerator;

    public function __construct(ParameterBagInterface $params, KernelInterface  $kernelInterface, UrlGeneratorInterface $urlGenerator) {
        $this->pathImage = $params->get('base_url'). $params->get('pathPublic') . "trajetroute/";
        $this->pathImageTrajetroute = $params->get('pathImageTrajetroute');
        $this->pathPublic = $params->get('pathPublic');
        $this->pathForNamePhotoTrajetroute = $params->get('pathForNamePhotoTrajetroute');
        $this->kernelInterface = $kernelInterface;
        $this->directoryCopy= $kernelInterface->getProjectDir()."/public".$params->get('pathPublic')."trajetroute/";
        $this->urlGenerator = $urlGenerator;
    }


    /**
     * @Route("/api/trajetroute/updatephoto", name="trajetroute_update_photo", methods={"POST"})
     */
    public function updatePhoto(Request $request, TrajetrouteService $trajetrouteService)
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
            $setUpdate = "";

            $infoPhotosInfra = $trajetrouteService->getPhotoInfraInfo($idInfra);
            $toDeletePhoto1 = false;
            $toDeletePhoto2 = false;
            $toDeletePhoto3 = false;
            $toNullPhoto1 = false;
            $toNullPhoto2 = false;
            $toNullPhoto3 = false;
            $oldPhotosInfra = [];
            if ($infoPhotosInfra != false && count($infoPhotosInfra) > 0) {
                if (isset($infoPhotosInfra[0]["photo1"])) {
                    $toDeletePhoto1 = true;
                    $oldPhotosInfra["photo1"] = $infoPhotosInfra[0]["photo1"];
                }

                if (isset($infoPhotosInfra[0]["photo2"])) {
                    $toDeletePhoto2 = true;
                    $oldPhotosInfra["photo2"] = $infoPhotosInfra[0]["photo2"];
                }

                if (isset($infoPhotosInfra[0]["photo3"])) {
                    $toDeletePhoto3 = true;
                    $oldPhotosInfra["photo3"] = $infoPhotosInfra[0]["photo3"];
                }
            }


            $directory1 = $this->pathImageTrajetroute . "photo1/";

            if (null != $uploadedFile1) {
                $nomOriginal1 = $uploadedFile1->getClientOriginalName();
                $tmpPathName1 = $uploadedFile1->getPathname();

                $directoryPublicCopy =  $this->directoryCopy. "photo1/";    
                //$name_temp = hash('sha512', session_id().microtime($nomOriginal1));
                $nomPhoto1 = uniqid().".".$uploadedFile1->getClientOriginalExtension();
                
                move_uploaded_file($tmpPathName1, $directory1.$nomPhoto1);
                copy($directory1.$nomPhoto1, $directoryPublicCopy.$nomPhoto1);

                $data['photo1'] = $this->pathForNamePhotoTrajetroute."photo1/" .$nomPhoto1;
                $data['photoName1'] = $nomPhoto1;
                $setUpdate .= "photo1 = '".$data['photo1']."', photo_name1 = '".$data['photoName1']."'";

                if ($toDeletePhoto1) {
                    $nomOldFile1 = basename($oldPhotosInfra["photo1"]);
                    if (file_exists($directory1.$nomOldFile1)) {
                        unlink($directory1.$nomOldFile1);
                        unlink($directoryPublicCopy.$nomOldFile1);
                    }
                }
                
            } else {
                if ($toDeletePhoto1) {
                    $nomOldFile1 = basename($oldPhotosInfra["photo1"]);
                    $directoryPublicCopy =  $this->directoryCopy. "photo1/";
                    if (file_exists($directory1.$nomOldFile1)) {
                        unlink($directory1.$nomOldFile1);
                        unlink($directoryPublicCopy.$nomOldFile1);
                    }
                }
                $toNullPhoto1 = true;
                $setUpdate .= "photo1 = null, photo_name1 = null";
            }

            
            

            $directory2 = $this->pathImageTrajetroute . "photo2/";

            if (null != $uploadedFile2) {
                $nomOriginal2 = $uploadedFile2->getClientOriginalName();
                $tmpPathName2 = $uploadedFile2->getPathname();

                $directoryPublicCopy =  $this->directoryCopy. "photo2/";
                $name_temp2 = hash('sha512', session_id().microtime($nomOriginal2));
                $nomPhoto2 = uniqid().".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName2, $directory2.$nomPhoto2);
                copy($directory2.$nomPhoto2, $directoryPublicCopy.$nomPhoto2);
                
                $data['photo2'] = $this->pathForNamePhotoTrajetroute."photo2/" .$nomPhoto2;
                $data['photoName2'] = $nomPhoto2;
                if (null != $data['photo1']) {
                    $setUpdate .= ", ";    
                }

                $setUpdate .= "photo2 = '".$data['photo2']."', photo_name2 = '".$data['photoName2']."'";

                if ($toDeletePhoto2) {
                    $nomOldFile2 = basename($oldPhotosInfra["photo2"]);
                    if (file_exists($directory2.$nomOldFile2)) {
                        unlink($directory2.$nomOldFile2);
                        unlink($directoryPublicCopy.$nomOldFile2);
                    }
                }
            } else {
                if ($toDeletePhoto2) {
                    $nomOldFile2 = basename($oldPhotosInfra["photo2"]);
                    $directoryPublicCopy =  $this->directoryCopy. "photo2/";
                    if (file_exists($directory2.$nomOldFile2)) {
                        unlink($directory2.$nomOldFile2);
                        unlink($directoryPublicCopy.$nomOldFile2);
                    }
                }
                $toNullPhoto2 = true;
                if ($toNullPhoto1 || null != $data['photo1']) {
                    $setUpdate .= ", ";  
                }

                $setUpdate .= "photo2 = null, photo_name2 = null";
            }


            $directory3 = $this->pathImageTrajetroute . "photo3/";

            if (null != $uploadedFile3) {
                $nomOriginal3 = $uploadedFile3->getClientOriginalName();
                $tmpPathName3 = $uploadedFile3->getPathname();

                $directoryPublicCopy =  $this->directoryCopy. "photo3/";
                $name_temp3 = hash('sha512', session_id().microtime($nomOriginal3));
                $nomPhoto3 = uniqid().".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName3, $directory3.$nomPhoto3);
                copy($directory3.$nomPhoto3, $directoryPublicCopy.$nomPhoto3);

                $data['photo3'] = $this->pathForNamePhotoTrajetroute."photo3/" .$nomPhoto3;
                $data['photoName3'] = $nomPhoto3;

                if (null != $data['photo1'] || null != $data['photo2']) {
                    $setUpdate .= ", ";    
                }

                $setUpdate .= "photo3 = '".$data['photo3']."', photo_name3 = '".$data['photoName3']."'";

                if ($toDeletePhoto3) {
                    $nomOldFile3 = basename($oldPhotosInfra["photo3"]);
                    if (file_exists($directory3.$nomOldFile3)) {
                        unlink($directory3.$nomOldFile3);
                        unlink($directoryPublicCopy.$nomOldFile3);
                    }
                }
            } else {
                if ($toDeletePhoto3) {
                    $nomOldFile3 = basename($oldPhotosInfra["photo3"]);
                    $directoryPublicCopy =  $this->directoryCopy. "photo3/";
                    if (file_exists($directory3.$nomOldFile3)) {
                        unlink($directory3.$nomOldFile3);
                        unlink($directoryPublicCopy.$nomOldFile3);
                    }
                }
                $toNullPhoto3 = true;

                if ($toNullPhoto2  || null != $data['photo2']) {
                    $setUpdate .= ", ";  
                }

                $setUpdate .= "photo3 = null, photo_name3 = null";
            }

            if (isset($setUpdate) && !empty($setUpdate)) {
                $idInfra = $trajetrouteService->addInfrastructurePhoto($idInfra, $setUpdate);
            }
            

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Photo trajet route updated_successfull"
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
            $setUpdate = "";
            if (null != $uploadedFile1) {
                $nomOriginal1 = $uploadedFile1->getClientOriginalName();
                $tmpPathName1 = $uploadedFile1->getPathname();
                $directory1 = $this->pathImageTrajetroute . "photo1/";
                $directoryPublicCopy =  $this->directoryCopy. "photo1/";

                $name_temp = hash('sha512', session_id().microtime($nomOriginal1));
                $nomPhoto1 = uniqid().".".$uploadedFile1->getClientOriginalExtension();
                
                move_uploaded_file($tmpPathName1, $directory1.$nomPhoto1);
                copy($directory1.$nomPhoto1, $directoryPublicCopy.$nomPhoto1);

                $data['photo1'] = $this->pathForNamePhotoTrajetroute."photo1/" .$nomPhoto1;
                $data['photoName1'] = $nomPhoto1;
                $setUpdate .= "photo1 = '".$data['photo1']."', photo_name1 = '".$data['photoName1']."'";
            }
            
            
            if (null != $uploadedFile2) {
                $nomOriginal2 = $uploadedFile2->getClientOriginalName();
                $tmpPathName2 = $uploadedFile2->getPathname();
                $directory2 = $this->pathImageTrajetroute . "photo2/";
                $directoryPublicCopy =  $this->directoryCopy. "photo2/";

                $name_temp2 = hash('sha512', session_id().microtime($nomOriginal2));
                $nomPhoto2 = uniqid().".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName2, $directory2.$nomPhoto2);
                copy($directory2.$nomPhoto2, $directoryPublicCopy.$nomPhoto2);
                
                $data['photo2'] = $this->pathForNamePhotoTrajetroute."photo2/" .$nomPhoto2;
                $data['photoName2'] = $nomPhoto2;
                if (null != $data['photo1']) {
                    $setUpdate .= ", ";    
                }
                $setUpdate .= "photo2 = '".$data['photo2']."', photo_name2 = '".$data['photoName2']."'";
            }

            if (null != $uploadedFile3) {
                $nomOriginal3 = $uploadedFile3->getClientOriginalName();
                $tmpPathName3 = $uploadedFile3->getPathname();
                $directory3 = $this->pathImageTrajetroute . "photo3/";
                $directoryPublicCopy =  $this->directoryCopy. "photo3/";

                $name_temp3 = hash('sha512', session_id().microtime($nomOriginal3));
                $nomPhoto3 = uniqid().".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName3, $directory3.$nomPhoto3);
                copy($directory3.$nomPhoto3, $directoryPublicCopy.$nomPhoto3);

                $data['photo3'] = $this->pathForNamePhotoTrajetroute."photo3/" .$nomPhoto3;
                $data['photoName3'] = $nomPhoto3;

                if (null != $data['photo1'] || null != $data['photo2']) {
                    $setUpdate .= ", ";    
                }

                $setUpdate .= "photo3 = '".$data['photo3']."', photo_name3 = '".$data['photoName3']."'";
            }

            if (isset($setUpdate) && !empty($setUpdate)) {
                $idInfra = $trajetrouteService->addInfrastructurePhoto($idInfra, $setUpdate);
            }

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
            $data['sourceInformation' ] = $infos['sourceInformation'];
            $data['modeAcquisitionInformation' ] = $infos['modeAcquisitionInformation'];
            $data['photo1'] = null;
            $data['photo2'] = null;
            $data['photo3'] = null;
            $data['photoName1'] = null;
            $data['photoName2'] = null;
            $data['photoName3'] = null;
            $data['coordonnees'] = "";
            if (count($infos['localisations']) > 0) {
                
                foreach ($infos['localisations'] as $key => $value) {
                    if (count($infos['localisations']) - 1 == $key) {
                        $data['coordonnees'] .= (string) $value['latitude']." ". (string) $value['longitude'];
                    } else {
                        $data['coordonnees'] .= (string) $value['latitude']." ". (string) $value['longitude'].", ";
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
                    //$dateInformationData = new \DateTime($infos['data']['dateInformationData']);
                    //$data['dateInformationData'] = $dateInformationData;
                    $data['dateInformationData'] = null;
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
                    $dateContratTravaux = new \DateTime($infos['travaux']['dateContratTravaux']);
                    $data['dateContratTravaux'] = $dateContratTravaux;

                    $dateOrdreServiceTravaux = new \DateTime($infos['travaux']['dateOrdreServiceTravaux']);
                    $data['dateOrdreServiceTravaux'] = $dateOrdreServiceTravaux;

                    $data['idTitulaireTravaux'] = $infos['travaux']['idTitulaireTravaux'];
                    $data['resultatTravaux'] = $infos['travaux']['resultatTravaux'];
                    $data['motifRuptureContratTravaux'] = $infos['travaux']['motifRuptureContratTravaux'];
                    
                    $dateReceptionProvisoireTravaux = new \DateTime($infos['travaux']['dateReceptionProvisoireTravaux']);
                    $data['dateReceptionProvisoireTravaux'] = $dateReceptionProvisoireTravaux;

                    $dateReceptionDefinitiveTravaux = new \DateTime($infos['travaux']['dateReceptionDefinitiveTravaux']);
                    $data['dateReceptionDefinitiveTravaux'] = $dateReceptionDefinitiveTravaux;


                    $data['ingenieurReceptionProvisoireTravaux'] = $infos['travaux']['ingenieurReceptionProvisoireTravaux'];
                    $data['ingenieurReceptionDefinitiveTravaux'] = $infos['travaux']['ingenieurReceptionDefinitiveTravaux'];
                
                    $dateInformationTravaux = new \DateTime($infos['travaux']['dateInformationTravaux']);
                    $data['dateInformationTravaux'] = $dateInformationTravaux;


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
                    $data['materielsFouriniture'] = $infos['fourniture']['materielsFouriniture'];
                    
                    $data['consistanceContratFourniture'] = $infos['fourniture']['consistanceContratFourniture'];
                    $data['entiteFourniture'] = $infos['fourniture']['entiteFourniture'];
                    $data['modePassationFourniture'] = $infos['fourniture']['modePassationFourniture'];
                    $data['porteAppelOffreFourniture'] = $infos['fourniture']['porteAppelOffreFourniture'];
                    $data['montantFourniture'] = $infos['fourniture']['montantFourniture'];
                    $data['idTitulaireFourniture'] = $infos['fourniture']['idTitulaireFourniture'];
                    $data['numeroContratFourniture'] = $infos['fourniture']['numeroContratFourniture'];
                  
                    $dateContratFourniture = new \DateTime($infos['fourniture']['dateContratFourniture']);
                    $data['dateContratFourniture'] = $dateContratFourniture;

                    $dateOrdreFourniture = new \DateTime($infos['fourniture']['dateOrdreFourniture']);
                    $data['dateOrdreFourniture'] = $dateOrdreFourniture;


                    $dateReceptionProvisoireFourniture = new \DateTime($infos['fourniture']['dateReceptionProvisoireFourniture']);
                    $data['dateReceptionProvisoireFourniture'] = $dateReceptionProvisoireFourniture;

                    $dateReceptionDefinitiveFourniture = new \DateTime($infos['fourniture']['dateReceptionDefinitiveFourniture']);
                    $data['dateReceptionDefinitiveFourniture'] = $dateReceptionDefinitiveFourniture;


                    $data['ingenieurReceptionProvisoireFourniture'] = $infos['fourniture']['ingenieurReceptionProvisoireFourniture'];
                    $data['ingenieurReceptionDefinitiveFourniture'] = $infos['fourniture']['ingenieurReceptionDefinitiveFourniture'];
                
                    $dateInformationTravaux = new \DateTime($infos['travaux']['dateInformationTravaux']);
                    $data['dateInformationTravaux'] = $dateInformationTravaux;

                    $data['resultatFourniture'] = $infos['fourniture']['resultatFourniture'];
                    $data['raisonResiliationFourniture'] = $infos['fourniture']['raisonResiliationFourniture'];
                    $data['bailleurFourniture'] = $infos['fourniture']['bailleurFourniture'];
                    
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
           
                    $dateOrdreServiceEtude = new \DateTime($infos['etude']['dateOrdreServiceEtude']);
                    $data['dateOrdreServiceEtude'] = $dateOrdreServiceEtude;

                    $dateContratEtude = new \DateTime($infos['etude']['dateContratEtude']);
                    $data['dateContratEtude'] = $dateContratEtude;
                    $data['motifRuptureContratEtude'] = $infos['etude']['motifRuptureContratEtude'];
                                        
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
            dd($this->urlGenerator->generate('images_route', ['64b1501d625a7.jpg']));
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

                $colonneDate = ["date_information", "date_contrat", "date_ordre_service", "date_reception_provisoire", "date_reception_definitive"];

                if (array_key_exists('infrastructure', $data) && count($data['infrastructure']) > 0) {
                    $hasInfraChanged = true;
                    $i = 0;

                    if (array_key_exists("localisations", $data['infrastructure'])) {
                        $coordonnees = "";
                        if (count($data['infrastructure']['localisations']) > 0) {
                            
                            foreach ($data['infrastructure']['localisations'] as $key => $value) {
                                if (count($data['infrastructure']['localisations']) - 1 == $key) {
                                    $coordonnees .= (string) $value['latitude']." ". (string) $value['longitude'];
                                } else {
                                    $coordonnees .= (string) $value['latitude']." ". (string) $value['longitude'].", ";
                                }
                                
                            }
                        }

                        $updateColonneInfra .= "geom = ST_GeomFromText('LINESTRING(".$coordonnees.")'), ";
                    }
                    

                    foreach ($data['infrastructure'] as $colonne => $value) {
                        if (in_array($colonne, $colonneInteger)) {
                            $value = intval($value);
                            if ($colonne == "id" || $colonne == "gid") {
                                $idInfra = $value;
                            }

                        } elseif(in_array($colonne, $colonneFloat)) {  
                            $value = floatval($value);
                        } else {
                            if ($colonne != "localisations") {
                                $value = pg_escape_string($value);
                                $value = "'$value'";
                            }
                        }

                        if ($colonne != "id" && $colonne != "gid"  && $colonne != "localisations") {
                            if (count($data['infrastructure']) - 1 != $i) {
                                $updateColonneInfra .= $colonne."="."$value".", ";
                            } else {
                                $updateColonneInfra .= $colonne."="."$value";
                            }
                        } 
                        $i++;
                    }

                    $updateColonneInfra = trim($updateColonneInfra);
                    if (isset($updateColonneInfra[-1]) && $updateColonneInfra[-1] == ",") {
                        $updateColonneInfra = substr($updateColonneInfra, 0, strlen($updateColonneInfra) - 1);
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
                        } elseif (in_array($colonne, $colonneDate)) {
                            $date = new \DateTime($value);
                            $value = $date->format('Y-m-d H:i:s');
                            $value = "'$value'";
                        } else {
                            $value = pg_escape_string($value);
                            $value = "'$value'";
                        }

                        if ($colonne != "id" && $colonne != "gid") {
                            if (count($data['situations']) - 1 != $i) {
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

                    $updateColonneEtat = trim($updateColonneEtat);
                    if (isset($updateColonneEtat[-1]) && $updateColonneEtat[-1] == ",") {
                        $updateColonneEtat = substr($updateColonneEtat, 0, strlen($updateColonneEtat) - 1);
                    }

                    if ($valuesInsert) {
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idSituation == 0) {
                        $idSituation = $trajetrouteService->addInfoInTableByInfrastructure('t_tj_02_situation', $colonneInsert, $valuesInsert);
                    } else {
                        $idSituation = $trajetrouteService->updateInfrastructureTables('t_tj_02_situation', $idSituation, $updateColonneEtat);
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
                        } elseif (in_array($colonne, $colonneDate)) {
                            $date = new \DateTime($value);
                            $value = $date->format('Y-m-d H:i:s');
                            $value = "'$value'";
                        } else {
                            $value = pg_escape_string($value);
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

                    $updateColonneData = trim($updateColonneData);
                    if (isset($updateColonneData[-1]) && $updateColonneData[-1] == ",") {
                        $updateColonneData = substr($updateColonneData, 0, strlen($updateColonneData) - 1);
                    }

                    if ($valuesInsert) {
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idData == 0) {
                        $idData = $trajetrouteService->addInfoInTableByInfrastructure('t_tj_04_donnees_collectees', $colonneInsert, $valuesInsert);
                    } else {
                        $idData = $trajetrouteService->updateInfrastructureTables('t_tj_04_donnees_collectees', $idData, $updateColonneData);
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
                        } elseif (in_array($colonne, $colonneDate)) {
                            $date = new \DateTime($value);
                            $value = $date->format('Y-m-d H:i:s');
                            $value = "'$value'";
                        } else {
                            $value = pg_escape_string($value);
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

                    $updateColonneTravaux = trim($updateColonneTravaux);
                    if (isset($updateColonneTravaux[-1]) && $updateColonneTravaux[-1] == ",") {
                        $updateColonneTravaux = substr($updateColonneTravaux, 0, strlen($updateColonneTravaux) - 1);
                    }

                    if ($valuesInsert) {
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idTravaux == 0) {
                        $idTravaux = $trajetrouteService->addInfoInTableByInfrastructure('t_tj_05_travaux', $colonneInsert, $valuesInsert);
                    } else {
                        $idTravaux = $trajetrouteService->updateInfrastructureTables('t_tj_05_travaux', $idTravaux, $updateColonneTravaux);
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
                        } elseif (in_array($colonne, $colonneDate)) {
                            $date = new \DateTime($value);
                            $value = $date->format('Y-m-d H:i:s');
                            $value = "'$value'";
                        } else {
                            $value = pg_escape_string($value);
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

                    $updateColonneEtudes = trim($updateColonneEtudes);
                    if (isset($updateColonneEtudes[-1]) && $updateColonneEtudes[-1] == ",") {
                        $updateColonneEtudes = substr($updateColonneEtudes, 0, strlen($updateColonneEtudes) - 1);
                    }

                    if ($valuesInsert) {
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idEtudes == 0) {
                        $idEtudes = $trajetrouteService->addInfoInTableByInfrastructure('t_tj_07_etudes', $colonneInsert, $valuesInsert);
                    } else {
                        $idEtudes = $trajetrouteService->updateInfrastructureTables('t_tj_07_etudes', $idEtudes, $updateColonneEtudes);
                    }
                }

                // Etat
                $hasEtatChanged = false;
                $updateColonneEtat = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idEtat = 0;
                if (array_key_exists('etat', $data) && count($data['etat']) > 0) {
                    $hasEtatChanged = true;
                    $i = 0;
                    foreach ($data['etat'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idEtat = intval($value);
                        }
                        
                        if (in_array($colonne, $colonneInteger)) {
                            $value = intval($value);
                        } elseif (in_array($colonne, $colonneFloat)) {  
                            $value = floatval($value);
                        } elseif (in_array($colonne, $colonneDate)) {
                            $date = new \DateTime($value);
                            $value = $date->format('Y-m-d H:i:s');
                            $value = "'$value'";
                        } else {
                            $value = pg_escape_string($value);
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

                    $updateColonneEtat = trim($updateColonneEtat);
                    if (isset($updateColonneEtat[-1]) && $updateColonneEtat[-1] == ",") {
                        $updateColonneEtat = substr($updateColonneEtat, 0, strlen($updateColonneEtat) - 1);
                    }

                    if ($valuesInsert) {
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idEtat == 0) {
                        $idEtat = $trajetrouteService->addInfoInTableByInfrastructure('t_tj_03_etat', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneEtat) && !empty($updateColonneEtat)) {
                        $idEtat = $trajetrouteService->updateInfrastructureTables('t_tj_03_etat', $idEtat, $updateColonneEtat);
                        }
                    } 
                    
                }

                // Fourniture
                $hasEtudeChanged = false;
                $updateColonneFourniture = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idFourniture = 0;
                if (array_key_exists('fournitures', $data) && count($data['fournitures']) > 0) {
                    $hasEtudeChanged = true;
                    $i = 0;
                    foreach ($data['fournitures'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idFourniture = intval($value);
                        }
                        
                        if (in_array($colonne, $colonneInteger)) {
                            $value = intval($value);
                        } elseif (in_array($colonne, $colonneFloat)) {  
                            $value = floatval($value);
                        } elseif (in_array($colonne, $colonneDate)) {
                            $date = new \DateTime($value);
                            $value = $date->format('Y-m-d H:i:s');
                            $value = "'$value'";
                        } else {
                            $value = pg_escape_string($value);
                            $value = "'$value'";
                        }

                        if ($colonne != "id" && $colonne != "gid") {
                            if (count($data['fournitures']) - 1 != $i) {
                                $updateColonneFourniture .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                            } else {
                                $updateColonneFourniture .= $colonne."="."$value";
                                $colonneInsert .= $colonne;
                                $valuesInsert .= $value;
                            }
                            
                        } 
                        $i++;
                    }

                    $updateColonneFourniture = trim($updateColonneFourniture);
                    if (isset($updateColonneFourniture[-1]) && $updateColonneFourniture[-1] == ",") {
                        $updateColonneFourniture = substr($updateColonneFourniture, 0, strlen($updateColonneFourniture) - 1);
                    }

                    if ($valuesInsert) {
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idFourniture == 0) {
                        $idFourniture = $trajetrouteService->addInfoInTableByInfrastructure('t_tj_06_fourniture', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneFourniture) && !empty($updateColonneFourniture)) {
                        $idFourniture = $trajetrouteService->updateInfrastructureTables('t_tj_06_fourniture', $idFourniture, $updateColonneFourniture);
                        }
                    }
                }
            }
        
        
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Trajet route update_successfull"
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
