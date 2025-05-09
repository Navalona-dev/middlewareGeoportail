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
use DateTime;
use Symfony\Component\HttpClient\Exception\ServerException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class RouteController extends AbstractController
{
    private $pathImage = null;
    private $pathImageRoute = null;
    private $pathPublic = null;
    private $pathForNamePhotoRoute = null;
    private $kernelInterface;
    private $directoryCopy = null;
    private const nameRepertoireImage = 'se_route/t_se_ro_infrastructure/';

    public function __construct(ParameterBagInterface $params, KernelInterface  $kernelInterface) {
        $this->pathImage = $params->get('base_url'). $params->get('pathPublic') . self::nameRepertoireImage;
        $this->pathImageRoute = $params->get('pathImageRoute');
        $this->pathPublic = $params->get('pathPublic');
        $this->pathForNamePhotoRoute = $params->get('pathForNamePhotoRoute');
        $this->kernelInterface = $kernelInterface;
        $this->directoryCopy= $kernelInterface->getProjectDir()."/public".$params->get('pathPublic'). self::nameRepertoireImage;
    }

     /**
     * @Route("/api/route/getphoto/{id}", name="infra_route_photo", methods={"GET"})
     */
    public function getPhotosByInfra($id, Request $request, RouteService $routeService)
    {
        $infoPhotosInfra = [];
        $response = new Response();
        if (isset($id) && !empty($id)) {
            $infoPhotosInfra = $routeService->getPhotoInfraInfo($id);
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Info infrastructure successfull",
                'pathImage' => $this->pathImage,
                'data' => $infoPhotosInfra
            ]));
        }
        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "Info infrastructure successfull",
            'pathImage' => $this->pathImage,
            'data' => $infoPhotosInfra
        ]));
        return $response;
    }

    /**
     * @Route("/api/route/deletephoto", name="route_delete_photo", methods={"POST"})
     */
    public function deletePhoto(Request $request, RouteService $routeService)
    { 
        $response = new Response();
        $hasException = false;
        $idInfra = null;
        try {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $photo = $data['photo'];
                $idInfra = $data['infraId'];
                $indexPhoto = "photo";
                $indexPhotoName = "photo_name";
                if ($photo != null && $photo != "null") {
                    $indexPhoto .= $photo;
                    $indexPhotoName .= $photo;
                }
            
                
                $setUpdate = "";

                $infoPhotosInfra = $routeService->getPhotoInfraInfo($idInfra);
                
                $oldPhotosInfra = [];
                if ($infoPhotosInfra != false && count($infoPhotosInfra) > 0 && array_key_exists($indexPhoto, $infoPhotosInfra[0])) {
                    if (isset($infoPhotosInfra[0][$indexPhoto]) && !empty($infoPhotosInfra[0][$indexPhoto]) && $infoPhotosInfra[0][$indexPhoto] != "") {
                        $oldPhotosInfra[$indexPhoto] = $infoPhotosInfra[0][$indexPhoto];
                    }
                }

                $directory = $this->pathImageRoute . $indexPhoto."/";
                $directoryPublicCopy =  $this->directoryCopy. $indexPhoto."/";
                
                if (array_key_exists($indexPhoto, $oldPhotosInfra)) {
                    $nomOldFile = basename($oldPhotosInfra[$indexPhoto]);
                    if (file_exists($directory.$nomOldFile)) {
                        unlink($directory.$nomOldFile);
                        unlink($directoryPublicCopy.$nomOldFile);
                        $setUpdate .= "$indexPhoto = null, $indexPhotoName = null";
                    }
                
                    if (isset($setUpdate) && !empty($setUpdate)) {
                        $idInfra = $routeService->addInfrastructurePhoto($idInfra, $setUpdate);
                    }
                   
                    $response->setContent(json_encode([
                        'code'  => Response::HTTP_OK,
                        'status' => true,
                        'message' => "Photo route deleted_successfull"
                    ]));
                } else {
                    $response->setContent(json_encode([
                        'code'  => Response::HTTP_OK,
                        'status' => true,
                        'message' => "Pas de photo route supprimer"
                    ]));
                }
                
                $response->headers->set('Content-Type', 'application/json');
            }
            

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
     * @Route("/api/route/updatephoto", name="route_update_photo", methods={"POST"})
     */
    public function updatePhoto(Request $request, RouteService $routeService)
    { 
        $response = new Response();
        $hasException = false;
        $idInfra = null;
        try {
            $data = [];
            $uploadedFile1 = "undefined";
            $uploadedFile2 = "undefined";
            $uploadedFile3 = "undefined";
            if ($request->files->has('photo1')) {
                $uploadedFile1 = $request->files->get('photo1');
            }
            if ($request->files->has('photo2')) {
                $uploadedFile2 = $request->files->get('photo2');
            }
            if ($request->files->has('photo3')) {
                $uploadedFile3 = $request->files->get('photo3');
            }
            
            $idInfra = $request->get('infraId');
            $data['photo1'] = null;
            $data['photo2'] = null;
            $data['photo3'] = null;
            $data['photoName1'] = null;
            $data['photoName2'] = null;
            $data['photoName3'] = null;
            $setUpdate = "";

            $infoPhotosInfra = $routeService->getPhotoInfraInfo($idInfra);
            $toDeletePhoto1 = false;
            $toDeletePhoto2 = false;
            $toDeletePhoto3 = false;
            $toNullPhoto1 = false;
            $toNullPhoto2 = false;
            $toNullPhoto3 = false;
            $oldPhotosInfra = [];
            if ($infoPhotosInfra != false && count($infoPhotosInfra) > 0) {
                if (isset($infoPhotosInfra[0]["photo1"]) && !empty($infoPhotosInfra[0]["photo1"]) && $infoPhotosInfra[0]["photo1"] != "") {
                    $toDeletePhoto1 = true;
                    $oldPhotosInfra["photo1"] = $infoPhotosInfra[0]["photo1"];
                }

                if (isset($infoPhotosInfra[0]["photo2"]) && !empty($infoPhotosInfra[0]["photo2"]) && $infoPhotosInfra[0]["photo2"] != "") {
                    $toDeletePhoto2 = true;
                    $oldPhotosInfra["photo2"] = $infoPhotosInfra[0]["photo2"];
                }

                if (isset($infoPhotosInfra[0]["photo3"]) && !empty($infoPhotosInfra[0]["photo3"]) && $infoPhotosInfra[0]["photo3"] != "") {
                    $toDeletePhoto3 = true;
                    $oldPhotosInfra["photo3"] = $infoPhotosInfra[0]["photo3"];
                }
            }

            if(!is_dir($this->pathImageRoute)) {
                mkdir($this->pathImageRoute, 0777, true);
            }
          
            $directory1 = $this->pathImageRoute . "photo1/";
      
            if (null != $uploadedFile1 && "null" != $uploadedFile1 && "undefined" != $uploadedFile1) {
                $nomOriginal1 = $uploadedFile1->getClientOriginalName();
                $tmpPathName1 = $uploadedFile1->getPathname();
                $directoryPublicCopy =  $this->directoryCopy. "photo1/";    

                if(!is_dir($directory1)) {
                    mkdir($directory1, 0777, true);
                }

                if(!is_dir($directoryPublicCopy)) {
                    mkdir($directoryPublicCopy, 0777, true);
                } 

                
                //$name_temp = hash('sha512', session_id().microtime($nomOriginal1));
                $nomPhoto1 = uniqid().".".$uploadedFile1->getClientOriginalExtension();
                
                move_uploaded_file($tmpPathName1, $directory1.$nomPhoto1);
                //copy($directory1.$nomPhoto1, $directoryPublicCopy.$nomPhoto1);

                $data['photo1'] = $this->pathForNamePhotoRoute."photo1/" .$nomPhoto1;
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
                if ($toDeletePhoto1 && ("null" == $uploadedFile1 || null == $uploadedFile1)) {
                    $nomOldFile1 = basename($oldPhotosInfra["photo1"]);
                    $directoryPublicCopy =  $this->directoryCopy. "photo1/";
                    if (file_exists($directory1.$nomOldFile1)) {
                        unlink($directory1.$nomOldFile1);
                        unlink($directoryPublicCopy.$nomOldFile1);
                    }
                }
                
                if ($uploadedFile1 != "undefined") {
                    $toNullPhoto1 = true;
                    $setUpdate .= "photo1 = null, photo_name1 = null";
                }
            }
        

            $directory2 = $this->pathImageRoute . "photo2/";

            if (null != $uploadedFile2 && "null" != $uploadedFile2 && "undefined" != $uploadedFile2) {
                $nomOriginal2 = $uploadedFile2->getClientOriginalName();
                $tmpPathName2 = $uploadedFile2->getPathname();

                $directoryPublicCopy =  $this->directoryCopy. "photo2/";

                if(!is_dir($directory2)) {
                    mkdir($directory2, 0777, true);
                }

                if(!is_dir($directoryPublicCopy)) {
                    mkdir($directoryPublicCopy, 0777, true);
                } 

                $name_temp2 = hash('sha512', session_id().microtime($nomOriginal2));
                $nomPhoto2 = uniqid().".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName2, $directory2.$nomPhoto2);
                //copy($directory2.$nomPhoto2, $directoryPublicCopy.$nomPhoto2);
                
                $data['photo2'] = $this->pathForNamePhotoRoute."photo2/" .$nomPhoto2;
                $data['photoName2'] = $nomPhoto2;
                //if (null != $data['photo1']) {
                    if ($uploadedFile1 != "undefined" || $toNullPhoto1 || null != $data['photo1']) {
                        $setUpdate .= ", ";    
                    }
                //}
               
                $setUpdate .= "photo2 = '".$data['photo2']."', photo_name2 = '".$data['photoName2']."'";

                if ($toDeletePhoto2) {
                    $nomOldFile2 = basename($oldPhotosInfra["photo2"]);
                    if (file_exists($directory2.$nomOldFile2)) {
                        unlink($directory2.$nomOldFile2);
                        unlink($directoryPublicCopy.$nomOldFile2);
                    }
                }
            } else {
                if ($toDeletePhoto2 && ("null" == $uploadedFile2 || null == $uploadedFile2)) {
                    $nomOldFile2 = basename($oldPhotosInfra["photo2"]);
                    $directoryPublicCopy =  $this->directoryCopy. "photo2/";
                    if (file_exists($directory2.$nomOldFile2)) {
                        unlink($directory2.$nomOldFile2);
                        unlink($directoryPublicCopy.$nomOldFile2);
                    }
                }

                if (($toNullPhoto1 || null != $data['photo1'] || "undefined" != $uploadedFile1) && $uploadedFile2 != "undefined") {
                    $setUpdate .= ", ";  
                }
                if ($uploadedFile2 != "undefined") {
                    $setUpdate .= "photo2 = null, photo_name2 = null";
                    $toNullPhoto2 = true;
                }
                
            }


            $directory3 = $this->pathImageRoute . "photo3/";
           
            if (null != $uploadedFile3 && "null" != $uploadedFile3 && "undefined" != $uploadedFile3) {
                $nomOriginal3 = $uploadedFile3->getClientOriginalName();
                $tmpPathName3 = $uploadedFile3->getPathname();

                $directoryPublicCopy =  $this->directoryCopy. "photo3/";

                if(!is_dir($directory3)) {
                    mkdir($directory3, 0777, true);
                }

                if(!is_dir($directoryPublicCopy)) {
                    mkdir($directoryPublicCopy, 0777, true);
                } 

                $name_temp3 = hash('sha512', session_id().microtime($nomOriginal3));
                $nomPhoto3 = uniqid().".".$uploadedFile3->getClientOriginalExtension();
                move_uploaded_file($tmpPathName3, $directory3.$nomPhoto3);
                //copy($directory3.$nomPhoto3, $directoryPublicCopy.$nomPhoto3);

                $data['photo3'] = $this->pathForNamePhotoRoute."photo3/" .$nomPhoto3;
                $data['photoName3'] = $nomPhoto3;
               
                if (null != $data['photo1'] || null != $data['photo2'] || "undefined" != $uploadedFile2 || "undefined" != $uploadedFile1 || $toNullPhoto1 || $toNullPhoto2) {
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
                if ($toDeletePhoto3 && ("null" == $uploadedFile3 || null == $uploadedFile3)) {
                    $nomOldFile3 = basename($oldPhotosInfra["photo3"]);
                    $directoryPublicCopy =  $this->directoryCopy. "photo3/";
                    if (file_exists($directory3.$nomOldFile3)) {
                        unlink($directory3.$nomOldFile3);
                        unlink($directoryPublicCopy.$nomOldFile3);
                    }
                }
               
               
                if (($toNullPhoto2  || null != $data['photo2'] || $toNullPhoto1 || "undefined" != $uploadedFile2 || "undefined" != $uploadedFile1) && $uploadedFile3 != "undefined") {
                    $setUpdate .= ", ";  
                }
                //dd($toNullPhoto2, $setUpdate, $data, $uploadedFile3, $uploadedFile3);
                if ($uploadedFile3 != "undefined") {
                    $setUpdate .= "photo3 = null, photo_name3 = null";
                    $toNullPhoto3 = true;
                }
               
            }
           
            
         
            if (isset($setUpdate) && !empty($setUpdate)) {
                $idInfra = $routeService->addInfrastructurePhoto($idInfra, $setUpdate);
            }
            

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Photo route updated_successfull"
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
     * @Route("/api/route/add", name="route_add", methods={"POST"})
     */
    public function create(Request $request, RouteService $routeService)
    {    
        $response = new Response();
        $hasException = false;
        $idInfra = null;
        try {

            $data = [];
            $data['region' ] = $request->get('region');
            $data['district' ] = $request->get('district');
            $data['commune' ] = $request->get('commune');
            $data['localite' ] = $request->get('localite');
            //$data['rattache' ] = $request->get('rattache');
            $data['rattache'] = null;

            if ($request->get('rattache') != "null" && $request->get('rattache') != "undefined") {
                    $infoYlisteRoute = $routeService->getInfoyRouteInfoMinifie($request->get('rattache'));
                   
                    if ($infoYlisteRoute != false && count($infoYlisteRoute) > 0) {
                        $data['rattache'] = $infoYlisteRoute[0]['nom'];
                    }
            }

            $data['categorie' ] = $request->get('categorie');
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
            $data['structure'] = $request->get('structure');
            $data['procedureTravaux'] = $request->get('procedureTravaux');
            $data['precisionStructure'] = $request->get('precisionStructure');
            $data['precisionModeGestion'] = $request->get('precisionModeGestion');
            $data['etat'] = $request->get('etat');
            $data['fonctionnel'] = $request->get('fonctionnel');
            $data['causeNonFonctionel'] = $request->get('causeNonFonctionel');
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
            //$data['structureOrnierage'] = $request->get('structureOrnierage');
            
            $data['dateInformationAccotement'] = null;
            $data['sourceInformationAccotement'] = null;
            $data['modeAcquisitionInformationAccotement'] = null;
            if (null != $request->get('dateInformationAccotement') && $request->get('dateInformationAccotement') != "null" && $request->get('dateInformationAccotement') != "undefined") {
                $dateInformationAccotement = new \DateTime($request->get('dateInformationAccotement'));
                $dateInformationAccotement->format('Y-m-d H:i:s');
                $data['dateInformationAccotement'] = $dateInformationAccotement;
                $data['sourceInformationAccotement' ] = $request->get('sourceInformationAccotement');
                $data['modeAcquisitionInformationAccotement' ] = $request->get('modeAcquisitionInformationAccotement');
    
            }
           
            
            $uploadedFile1 = $request->files->get('photo1');
            $uploadedFile2 = $request->files->get('photo2');
            $uploadedFile3 = $request->files->get('photo3');
            $data['photo1'] = null;
            $data['photo2'] = null;
            $data['photo3'] = null;
            $data['photoName1'] = null;
            $data['photoName2'] = null;
            $data['photoName3'] = null;
            if (null != $uploadedFile1) {
                $nomOriginal1 = $uploadedFile1->getClientOriginalName();
                $tmpPathName1 = $uploadedFile1->getPathname();
                $directory1 = $this->pathImageRoute . "photo1/";
                $directoryPublicCopy =  $this->directoryCopy. "photo1/";

                $name_temp = hash('sha512', session_id().microtime($nomOriginal1));
                $nomPhoto1 = uniqid().".".$uploadedFile1->getClientOriginalExtension();
                
                move_uploaded_file($tmpPathName1, $directory1.$nomPhoto1);
                //copy($directory1.$nomPhoto1, $directoryPublicCopy.$nomPhoto1);

                $data['photo1'] = $this->pathForNamePhotoRoute."photo1/" .$nomPhoto1;
                $data['photoName1'] = $nomPhoto1;
            }
            
            if (null != $uploadedFile2) {
                $nomOriginal2 = $uploadedFile2->getClientOriginalName();
                $tmpPathName2 = $uploadedFile2->getPathname();
                $directory2 = $this->pathImageRoute . "photo2/";
                $directoryPublicCopy =  $this->directoryCopy. "photo2/";

                $name_temp2 = hash('sha512', session_id().microtime($nomOriginal2));
                $nomPhoto2 = uniqid().".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName2, $directory2.$nomPhoto2);
                //copy($directory2.$nomPhoto2, $directoryPublicCopy.$nomPhoto2);
                
                $data['photo2'] = $this->pathForNamePhotoRoute."photo2/" .$nomPhoto2;
                $data['photoName2'] = $nomPhoto2;
            }

            if (null != $uploadedFile3) {
                $nomOriginal3 = $uploadedFile3->getClientOriginalName();
                $tmpPathName3 = $uploadedFile3->getPathname();
                $directory3 = $this->pathImageRoute . "photo3/";
                $directoryPublicCopy =  $this->directoryCopy. "photo3/";

                $name_temp3 = hash('sha512', session_id().microtime($nomOriginal3));
                $nomPhoto3 = uniqid().".".$uploadedFile3->getClientOriginalExtension();
                move_uploaded_file($tmpPathName3, $directory3.$nomPhoto3);
                //copy($directory2.$nomPhoto2, $directoryPublicCopy.$nomPhoto2);

                $data['photo3'] = $this->pathForNamePhotoRoute."photo3/" .$nomPhoto3;
                $data['photoName3'] = $nomPhoto3;
            }

            $idInfra = $routeService->addInfrastructureRoute($data);
            $idTravaux = null;
            $idFoncier = null;
            $idEtude = null;
            $idFourniture = null;
            $idAccotementGauche = null;
            $idAccotementDroite = null;
            $idFosseGauche = null;
            $idFosseDroite = null;
            if ($idInfra != false) {
                // add situation et etat
                //$idEtat = $routeService->addInfrastructureRouteEtat($idInfra, $data);

                $idSituation = $routeService->addInfrastructureRouteSituation($idInfra, $data);

                $idSurface = $routeService->addInfrastructureRouteSurface($idInfra, $data);

                $idStructure = $routeService->addInfrastructureRouteStructure($idInfra, $data);

                $data['accotementHasAccotementGauche'] = $request->get('accotementHasAccotementGauche');

                $data['accotementHasAccotementDroite'] = $request->get('accotementHasAccotementDroite');

                if ($data['accotementHasAccotementGauche'] == "OUI") {
                    $data['accotementGauche'] = "Gauche";
                    //$data['accotement'] = $request->get('accotement');
                    //$data['accotementIsAccotementNonRevetu'] = $request->get('accotementIsAccotementNonRevetu');
                    $data['accotementRevetueGauche'] = $request->get('accotementRevetueGauche');
                    $data['accotementTypeRevetementAccotementGauche'] = $request->get('accotementTypeRevetementAccotementGauche');
                    $data['accotementDegrationSurfaceGauche'] = $request->get('accotementDegrationSurfaceGauche');
                    $data['accotementDentelleRiveGauche'] = $request->get('accotementDentelleRiveGauche');
                    $data['accotementPrecisionTypeAccotement'] = null; //$request->get('accotementPrecisionTypeAccotement');

                    $data['accotementDenivellationChausseAccotementGauche'] = $request->get('accotementDenivellationChausseAccotementGauche');
                    $data['accotementDestructionAffouillementAccotementGauche'] = $request->get('accotementDestructionAffouillementAccotementGauche');
                    $data['accotementNonRevetueDeformationProfilGauche'] = $request->get('accotementNonRevetueDeformationProfilGauche');
                    $data['reseauDivers'] = $request->get('reseauDivers');
                    $data['typeReseau'] = $request->get('typeReseau');
                    $idAccotementGauche = $routeService->addInfrastructureRouteAccotement($idInfra, $data, "Gauche");
                }

                

                if ($data['accotementHasAccotementDroite'] == "OUI") {
                    $data['accotementDroite'] = "Droite";
                    //$data['accotement'] = $request->get('accotement');
                    //$data['accotementIsAccotementNonRevetu'] = $request->get('accotementIsAccotementNonRevetu');
                    $data['accotementRevetueDroite'] = $request->get('accotementRevetueDroite');
                    $data['accotementTypeRevetementAccotementDroite'] = $request->get('accotementTypeRevetementAccotementDroite');
                    $data['accotementDegrationSurfaceDroite'] = $request->get('accotementDegrationSurfaceDroite');
                    $data['accotementDentelleRiveDroite'] = $request->get('accotementDentelleRiveDroite');
                    $data['accotementPrecisionTypeAccotement'] = null; //$request->get('accotementPrecisionTypeAccotement');

                    $data['accotementDenivellationChausseAccotementDroite'] = $request->get('accotementDenivellationChausseAccotementDroite');
                    $data['accotementDestructionAffouillementAccotementDroite'] = $request->get('accotementDestructionAffouillementAccotementDroite');
                    $data['accotementNonRevetueDeformationProfilDroite'] = $request->get('accotementNonRevetueDeformationProfilDroite');
                    $data['reseauDivers'] = $request->get('reseauDivers');
                    $data['typeReseau'] = $request->get('typeReseau');
                    $idAccotementDroite = $routeService->addInfrastructureRouteAccotement($idInfra, $data, "Droite");
                }

                
                
                $data['sourceInformationFosse' ] = $request->get('sourceInformationFosse');
                $data['modeAcquisitionInformationFosse' ] = $request->get('modeAcquisitionInformationFosse');

                if ($request->get('coteFosseGauche') == "OUI") {
                    $dateInformationFosse = new \DateTime($request->get('dateInformationFosse'));
                    $dateInformationFosse->format('Y-m-d H:i:s');
                    $data['dateInformationFosse'] = $dateInformationFosse;
                    $data['fosseRevetuGauche'] = "NON";
                    if ('null' != $request->get('fosseRevetuGauche') && null != $request->get('fosseRevetuGauche') && !empty($request->get('fosseRevetuGauche'))) {
                        $data['fosseRevetuGauche'] = $request->get('fosseRevetuGauche');
                    }
                    
                    $data['fosseRevetuDegradationFosseGauche'] = $request->get('fosseRevetuDegradationFosseGauche');
                    $data['fosseRevetuSectionBoucheGauche'] = $request->get('fosseRevetuSectionBoucheGauche');
                    $data['fosseNonRevetuFosseProfilGauche'] = $request->get('fosseNonRevetuFosseProfilGauche');
                    $data['fosseNonRevetuEncombrementGauche'] = $request->get('fosseNonRevetuEncombrementGauche');
        
                    
                    $data['coteFosseGauche'] = "Gauche";

                    $idFosseGauche = $routeService->addInfrastructureRouteFosse($idInfra, $data, "Gauche");
                }

                if ($request->get('coteFosseDroite') == "OUI") {
                    $dateInformationFosse = new \DateTime($request->get('dateInformationFosse'));
                    $dateInformationFosse->format('Y-m-d H:i:s');
                    $data['dateInformationFosse'] = $dateInformationFosse;
                    $data['fosseRevetuDroite'] = "NON";
                    if ('null' != $request->get('fosseRevetuDroite') && null != $request->get('fosseRevetuDroite') && !empty($request->get('fosseRevetuDroite'))) {
                        $data['fosseRevetuDroite'] = $request->get('fosseRevetuDroite');
                    }

                    $data['fosseRevetuDegradationFosseDroite'] = $request->get('fosseRevetuDegradationFosseDroite');
                    $data['fosseRevetuSectionBoucheDroite'] = $request->get('fosseRevetuSectionBoucheDroite');
                    $data['fosseNonRevetuFosseProfilDroite'] = $request->get('fosseNonRevetuFosseProfilDroite');
                    $data['fosseNonRevetuEncombrementDroite'] = $request->get('fosseNonRevetuEncombrementDroite');
        
                    
                    $data['coteFosseDroite'] = "Droite";

                    $idFosseDroite = $routeService->addInfrastructureRouteFosse($idInfra, $data, "Droite");
                }

                
            

                /**
                 * Administrative data
                 */
                //Foncier
                
                if (('null' != $request->get('hasFoncier') && null != $request->get('hasFoncier')) && ($request->get('hasFoncier') == true || $request->get('hasFoncier') == "true") && "false" != $request->get('hasFoncier')) {
                    $data['statut'] = $request->get('statutFoncier');
                    $data['numeroReference'] = $request->get('numeroReferenceFoncier');
                    $data['nomProprietaire'] = $request->get('nomProprietaireFoncier');
                    $idFoncier = $routeService->addInfrastructureRouteFoncier($idInfra, $data);
    
                }
               
                //Travaux 
                if (('null' != $request->get('hasTravaux') && null != $request->get('hasTravaux')) && ($request->get('hasTravaux') == true || $request->get('hasTravaux') == "true") && "false" != $request->get('hasTravaux')) {

                    $data['objetTravaux'] = $request->get('objetTravaux');
                    $data['consistanceTravaux'] = $request->get('consistanceTravaux');
                    $data['modeRealisationTravaux'] = $request->get('modeRealisationTravaux');
                    $data['maitreOuvrageTravaux'] = $request->get('maitreOuvrageTravaux');
                    $data['maitreOeuvreTravaux'] = $request->get('maitreOeuvreTravaux');
                    $data['maitreOuvrageDelegueTravaux'] = $request->get('maitreOuvrageDelegueTravaux');
                    $data['idControleSurveillanceTravaux'] = $request->get('idControleSurveillanceTravaux');//idControleSurveillance
                    $data['modePassationTravaux'] = null;

                    if (null != $request->get('modePassationTravaux')) {
                        $data['modePassationTravaux'] = $request->get('modePassationTravaux');
                    }
                    
                    $data['porteAppelOffreTravaux'] = $request->get('porteAppelOffreTravaux');
                    $data['montantTravaux'] = $request->get('montantTravaux');
                    $data['numeroContratTravaux'] = $request->get('numeroContratTravaux');
                    $data['precisionConsistanceTravaux'] = $request->get('precisionConsistanceTravaux');
                    
                    $dateContratTravaux = new \DateTime($request->get('dateContratTravaux'));
                    $dateContratTravaux->format('Y-m-d H:i:s');
                    $data['dateContratTravaux'] = $dateContratTravaux;

                    $dateOrdreServiceTravaux = new \DateTime($request->get('dateOrdreServiceTravaux'));
                    $dateOrdreServiceTravaux->format('Y-m-d H:i:s');

                    $data['dateOrdreServiceTravaux'] = $dateOrdreServiceTravaux;
                    $data['idTitulaireTravaux'] = $request->get('idTitulaireTravaux');//idTitulaire
                    $data['resultatTravaux'] = $request->get('resultatTravaux');
                    $data['motifRuptureContratTravaux'] = $request->get('motifRuptureContratTravaux');
                    $dateReceptionProvisoireTravaux = new \DateTime($request->get('dateReceptionProvisoireTravaux'));
                    $dateReceptionProvisoireTravaux->format('Y-m-d H:i:s');
                    $data['dateReceptionProvisoireTravaux'] = $dateReceptionProvisoireTravaux;
                    $dateReceptionDefinitiveTravaux = new \DateTime($request->get('dateReceptionDefinitiveTravaux'));
                    $dateReceptionDefinitiveTravaux->format('Y-m-d H:i:s');
                    $data['dateReceptionDefinitiveTravaux'] = $dateReceptionDefinitiveTravaux;
                    $data['ingenieurReceptionProvisoireTravaux'] = $request->get('ingenieurReceptionProvisoireTravaux');
                    $data['ingenieurReceptionDefinitiveTravaux'] = $request->get('ingenieurReceptionDefinitiveTravaux');
                    $data['dateInformationTravaux'] = new \DateTime();
                    $data['sourceInformationTravaux'] = $request->get('sourceInformationTravaux');
                    $data['modeAcquisitionInformationTravaux'] = $request->get('modeAcquisitionInformationTravaux');
                    $data['bailleurTravaux'] = $request->get('bailleurTravaux');

                    $idTravaux = $routeService->addInfrastructureRouteTravaux($idInfra, $data);
                }
                
                // Fournitures
                if (('null' != $request->get('hasFourniture') && null != $request->get('hasFourniture')) && ($request->get('hasFourniture') == true || $request->get('hasFourniture') == "true") && "false" != $request->get('hasFourniture')) {
                    $data['objetContratFourniture'] = $request->get('objetContratFourniture');
                    $data['consistanceContratFourniture'] = $request->get('consistanceContratFourniture');
                    $data['materielsFourniture'] = $request->get('materielsFourniture');
                    $data['entiteFourniture'] = $request->get('entiteFourniture');
                    $data['modePassationFourniture'] = $request->get('modePassationFourniture');
                    $data['porteAppelOffreFourniture'] = $request->get('porteAppelOffreFourniture');
                    $data['montantFourniture'] = $request->get('montantFourniture');
                    $data['idTitulaireFourniture'] = $request->get('idTitulaireFourniture');
                    $data['numeroContratFourniture'] = $request->get('numeroContratFourniture');
                    $dateContratFourniture = new \DateTime($request->get('dateContratFourniture'));
                    $dateContratFourniture->format('Y-m-d H:i:s');

                    $data['dateContratFourniture'] = $dateContratFourniture;

                    $dateOrdreFourniture = new \DateTime($request->get('dateOrdreFourniture'));
                    $dateOrdreFourniture->format('Y-m-d H:i:s');

                    $data['dateOrdreFourniture'] = $dateOrdreFourniture;
                    $data['resultatFourniture'] = $request->get('resultatFourniture');
                    $data['raisonResiliationFourniture'] = $request->get('raisonResiliationFourniture');
                    $data['ingenieurReceptionProvisoireFourniture'] = $request->get('ingenieurReceptionProvisoireFourniture');
                    $data['ingenieurReceptionDefinitiveFourniture'] = $request->get('ingenieurReceptionDefinitiveFourniture');
                    
                    $dateReceptionProvisoireFourniture = new \DateTime($request->get('dateReceptionProvisoireFourniture'));
                    $dateReceptionProvisoireFourniture->format('Y-m-d H:i:s');

                    $data['dateReceptionProvisoireFourniture'] = $dateReceptionProvisoireFourniture;
                    
                    $dateReceptionDefinitiveFourniture = new \DateTime($request->get('dateReceptionDefinitiveFourniture'));
                    $dateReceptionDefinitiveFourniture->format('Y-m-d H:i:s');

                    $data['dateReceptionDefinitiveFourniture'] = $dateReceptionDefinitiveFourniture;
                    $data['bailleurFourniture'] = $request->get('bailleurFourniture');
                    $idFourniture = $routeService->addInfrastructureRouteFourniture($idInfra, $data);
                }
                
                // Etudes
                if (('null' != $request->get('hasEtude') && null != $request->get('hasEtude')) && ($request->get('hasEtude') == true || $request->get('hasEtude') == "true") && "false" != $request->get('hasEtude')) {
                    $data['objetContratEtude'] = $request->get('objetContratEtude');
                    $data['consistanceContratEtude'] = $request->get('consistanceContratEtude');
                    $data['entiteEtude'] = $request->get('entiteEtude');
                    $data['idTitulaireEtude'] = $request->get('idTitulaireEtude');
                    $data['montantContratEtude'] = $request->get('montantContratEtude');
                    $data['numeroContratEtude'] = null ;
                    if (null != $request->get('numeroContratEtude')) {
                        $data['numeroContratEtude'] = $request->get('numeroContratEtude');
                    }
                    
                    $data['modePassationEtude'] = $request->get('modePassationEtude');
                    $data['porteAppelOffreEtude'] = $request->get('porteAppelOffreEtude');

                    $dateContratEtude = new \DateTime($request->get('dateContratEtude'));
                    $dateContratEtude->format('Y-m-d H:i:s');

                    $data['dateContratEtude'] = $dateContratEtude;

                    $dateOrdreServiceEtude = new \DateTime($request->get('dateOrdreServiceEtude'));
                    $dateOrdreServiceEtude->format('Y-m-d H:i:s');

                    $data['dateOrdreServiceEtude'] = $dateOrdreServiceEtude;
                    $data['resultatPrestationEtude'] = $request->get('resultatPrestationEtude');
                    /*if (null != $request->get('resultatPrestationEtude') && strlen($request->get('resultatPrestationEtude')) <= 20) {
                        $data['resultatPrestationEtude'] = $request->get('resultatPrestationEtude');
                    } else {
                    throw new \Exception("Resultat prestation etude doit etre une chaine au maximal 20 caractere");
                    }*/
                    
                    $data['motifRuptureContratEtude'] = $request->get('motifRuptureContratEtude');
                    
                    $dateInformationEtude = new \DateTime($request->get('dateInformationEtude'));
                    $dateInformationEtude->format('Y-m-d H:i:s');
                    
                    $data['dateInformationEtude'] = $dateInformationEtude;
                    $data['sourceInformationEtude'] = $request->get('sourceInformationEtude');
                    $data['modeAcquisitionInformationEtude'] = $request->get('modeAcquisitionInformationEtude');
                    $data['precisionConsistanceContratEtude'] = $request->get('precisionConsistanceContratEtude');
                    $data['bailleurEtude'] = $request->get('bailleurEtude');
                    $idEtude = $routeService->addInfrastructureRouteEtudes($idInfra, $data);
                }
                
                /**
                 * End Administrative data
                */
                //$idDonneAnnexe = $routeService->addInfrastructureEducationDonneAnnexe($idInfra, $data);
            }


                /*'idInfra'=> $idInfra,
                'sqlTravaux'=> $idTravaux,
                'sqlFoncier'=> $idFoncier,
                'sqlEtude'=> $idEtude,
                'sqlFourniture'=> $idFourniture,
                'sqlAccotementGauche'=> $idAccotementGauche,
                'sqlAccotementDroite'=> $idAccotementDroite,
                'sqlFosseGauche'=> $idFosseGauche,
                'sqlFosseDroite'=> $idFosseDroite,*/
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route created_successfull"
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
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'situation');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'surface');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'structure');
            ////$routeService->cleanTablesByIdInfrastructure($idInfra, 'etat');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'accotement');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'fosse');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'etude');
        }
        
        return $response;
    }

    /**
     * @Route("/api/infra/fosse/route/{id}", name="route_fosse", methods={"GET"})
     */
    public function getFosseRoute($id = null, Request $request, RouteService $routeService)
    {    
        $response = new Response();
        
        try {

            $routes = $routeService->getFosseRoute($id);

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route fosse_successfull",
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
     * @Route("/api/infra/accotement/route/{id}", name="route_accotement", methods={"GET"})
     */
    public function getAccotementRoute($id = null, Request $request, RouteService $routeService)
    {    
        $response = new Response();
        
        try {

            $routes = $routeService->getAccotementRoute($id);

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route accotement_successfull",
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
     * @Route("/api/infra/route/liste/minifie", name="route_list_minifie", methods={"GET"})
     */
    public function listeRouteMinifie(Request $request, RouteService $routeService)
    {    
        $response = new Response();
        
        try {

            $routes = $routeService->getAllInfrastructuresRouteMinifie();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route list_successfull",
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
     * @Route("/api/infra/route/info", name="route_info", methods={"POST"})
     */
    public function getOneInfraInfo(Request $request, RouteService $routeService)
    {    
        $response = new Response();
        
        try {
            $infraId = $request->get('id');

            $routes = $routeService->getOneInfraInfo(intval($infraId));
            $routesAccottement = $routeService->getAccotementRoute(intval($infraId));
            $routesFosse = $routeService->getFosseRoute(intval($infraId));
            $routesInfrastructure = $routeService->getAllyRouteInfoMinifie();
            $infoRoutes = [];
            if ($routes != false && $routesInfrastructure != false && count($routes) > 0 && count($routes) > 0 && count($routesInfrastructure) > 0 ) {
                foreach ($routesInfrastructure as $key => $value) {
                    
                   if (trim($value['nom']) == trim($routes[0]['rattache'])) {
                    $infoRoutes = $value;
                   }
                }
            
            }
           
            if ($routes != false && count($routes) > 0) {
                $routes[0]['accotements'] = false;
                if ($routesAccottement != false && count($routesAccottement) > 0) {
                    $routes[0]['accotements'] = $routesAccottement;
                }
                $routes[0]['fosses'] = false;
                if ($routesFosse != false && count($routesFosse) > 0) {
                    $routes[0]['fosses'] = $routesFosse;
                }
                $routes[0]['infoRoutes'] = false;
                if ($infoRoutes != false) {
                    $routes[0]['infoRoutes'] = $infoRoutes;
                }
            }
       
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Info infrastructure successfull",
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

    /**
     * @Route("/api/routes/information", name="infrastructure_consistance_information", methods={"GET"})
     */
    public function getAllyRouteInfo(Request $request, RouteService $routeService)
    {    
        //$routesInfrastructure = $routeService->getAllyRouteInfo();
        //
        $routesInfrastructure = $routeService->getAllyRouteInfoMinifie();
        $response = new Response();

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "route information list_successfull",
            'data' => $routesInfrastructure
        ]));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    /**
     * @Route("/api/route/update", name="route_update", methods={"POST"})
     */
    public function update(Request $request, RouteService $routeService)
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
                'id_ingenieurs_reception_definitive', 'montant_contrat', 'nombre_voies', 'pk_debut', 'pk_fin', 'capacite_de_voiture_accueillies'];
                $colonneFloat = ['longueur', 'largeur', 'charge_maximum', 'Largeur_chaussée', 'Largeur_accotements', 'decalage_de_la_jointure_du_tablier_chaussee_en_affaissement', 'decalage_de_la_jointure_du_tablier_chaussee_en_ecartement'];

                $colonneDate = ["date_information", "date_contrat", "date_ordre_service", "date_reception_provisoire", "date_reception_definitive"];
                
                if (array_key_exists('infrastructure', $data) && count($data['infrastructure']) > 0) {
                    $hasInfraChanged = true;
                    $i = 0;

                    if (array_key_exists("long", $data['infrastructure']) && array_key_exists("lat", $data['infrastructure'])) {
                        $updateColonneInfra .= "geom = ST_GeomFromText('POINT(" . $data['infrastructure']['long'] . " " . $data['infrastructure']['lat'] . ")'), ";
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
                            if ($colonne == "rattache") {
                                if ($value != "null" && $value != "undefined" && $value != "") {
                                    $infoYlisteRoute = $routeService->getInfoyRouteInfoMinifie($value);
                                    if ($infoYlisteRoute != false && count($infoYlisteRoute) > 0) {
                                        $value = $infoYlisteRoute[0]['nom'];
                                        $value = pg_escape_string($value);
                                        $value = "'$value'";
                                    }
                                }
                            } else {
                                $value = pg_escape_string($value);
                                $value = "'$value'";
                            }
                        }

                        if ($colonne != "id" && $colonne != "gid" && $colonne != "long" && $colonne != "lat") {
                            if (count($data['infrastructure']) - 1 != $i) {
                                if ($colonne == "Largeur_chaussée" || $colonne == "Largeur_accotements" || $colonne == "Structure") {
                                    $updateColonneInfra .= "\"$colonne\""."="."$value".", ";
                                } else {
                                    if ($colonne != "rattache") {
                                        $updateColonneInfra .= $colonne."="."$value".", ";
                                    } /*else {
                                        $value = pg_escape_string($value);
                                        $value = "'$value'";
                                        $updateColonneInfra .= $colonne."="."$value".", ";
                                    }*/
                                    
                                }
                                
                            } else {
                                if ($colonne == "Largeur_chaussée" || $colonne == "Largeur_accotements" || $colonne == "Structure") {
                                    $updateColonneInfra .= "\"$colonne\""."="."$value";
                                } else {
                                    if ($colonne != "rattache") {
                                        $updateColonneInfra .= $colonne."="."$value";
                                    }
                                    
                                }
                                
                            }
                        } 
                        $i++;
                    }
                    
                    $updateColonneInfra = trim($updateColonneInfra);
                    if (isset($updateColonneInfra[-1]) && $updateColonneInfra[-1] == ",") {
                        $updateColonneInfra = substr($updateColonneInfra, 0, strlen($updateColonneInfra) - 1);
                    }
                  
                    if (isset($updateColonneInfra) && !empty($updateColonneInfra)) {
                    $idInfra = $routeService->updateInfrastructure($idInfra, $updateColonneInfra);
                    }
                }

                // Fosse Gauche
                $hasEtatChanged = false;
                $updateColonneFosse = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idFosseGauche = 0;
                if (array_key_exists('fosseGauche', $data) && count($data['fosseGauche']) > 0) {
                    $hasEtatChanged = true;
                    $i = 0;
                    foreach ($data['fosseGauche'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idFosseGauche = intval($value);
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
                            if (count($data['fosseGauche']) - 1 != $i) {
                                $updateColonneFosse .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                            } else {
                                $updateColonneFosse .= $colonne."="."$value";
                                $colonneInsert .= $colonne;
                                $valuesInsert .= $value;
                            }
                        } 
                        $i++;
                    }

                    $updateColonneFosse = trim($updateColonneFosse);
                    if (isset($updateColonneFosse[-1]) && $updateColonneFosse[-1] == ",") {
                        $updateColonneFosse = substr($updateColonneFosse, 0, strlen($updateColonneFosse) - 1);
                    }

                    if ($valuesInsert) {
                        if ($idFosseGauche == 0) {
                            $date = new \DateTime();
                            $dateInfo = $date->format('Y-m-d H:i:s');
                            $colonneInsert .= "date_information";
                            $valuesInsert .= "'$dateInfo'";
                        }
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }
                    
                    if ($idFosseGauche == 0) {
                        $idFosseGauche = $routeService->addInfoInTableByInfrastructure('t_ro_08_fosse', $colonneInsert, $valuesInsert);
                    } else {
                        $idFosseGauche = $routeService->updateInfrastructureTables('t_ro_08_fosse', $idFosseGauche, $updateColonneFosse);
                    } 
                    
                }

                // Fosse Gauche
                $hasEtatChanged = false;
                $updateColonneFosse = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idFosseDroite = 0;
                if (array_key_exists('fosseDroite', $data) && count($data['fosseDroite']) > 0) {
                    $hasEtatChanged = true;
                    $i = 0;
                    foreach ($data['fosseDroite'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idFosseDroite = intval($value);
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
                            if (count($data['fosseDroite']) - 1 != $i) {
                                $updateColonneFosse .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                            } else {
                                $updateColonneFosse .= $colonne."="."$value";
                                $colonneInsert .= $colonne;
                                $valuesInsert .= $value;
                            }
                        } 
                        $i++;
                    }

                    $updateColonneFosse = trim($updateColonneFosse);
                    if (isset($updateColonneFosse[-1]) && $updateColonneFosse[-1] == ",") {
                        $updateColonneFosse = substr($updateColonneFosse, 0, strlen($updateColonneFosse) - 1);
                    }

                    if ($valuesInsert) {
                        if ($idFosseDroite == 0) {
                            $date = new \DateTime();
                            $dateInfo = $date->format('Y-m-d H:i:s');
                            $colonneInsert .= "date_information";
                            $valuesInsert .= "'$dateInfo'";
                        }
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idFosseDroite == 0) {
                        $idFosseDroite = $routeService->addInfoInTableByInfrastructure('t_ro_08_fosse', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneFosse) && !empty($updateColonneFosse)) {
                        $idFosseDroite = $routeService->updateInfrastructureTables('t_ro_08_fosse', $idFosseGauche, $updateColonneFosse);
                        }
                    } 
                    
                }

                // Accotement Gauche
                $hasEtatChanged = false;
                $updateColonneAccote = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idAccoteGauche = 0;
                if (array_key_exists('accotementGauche', $data) && count($data['accotementGauche']) > 0) {
                    $hasEtatChanged = true;
                    $i = 0;
                    foreach ($data['accotementGauche'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idAccoteGauche = intval($value);
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
                            if (count($data['accotementGauche']) - 1 != $i) {
                                $updateColonneAccote .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                            } else {
                                $updateColonneAccote .= $colonne."="."$value";
                                $colonneInsert .= $colonne;
                                $valuesInsert .= $value;
                            }
                        } 
                        $i++;
                    }

                    $updateColonneAccote = trim($updateColonneAccote);
                    if (isset($updateColonneAccote[-1]) && $updateColonneAccote[-1] == ",") {
                        $updateColonneAccote = substr($updateColonneAccote, 0, strlen($updateColonneAccote) - 1);
                    }

                    if ($valuesInsert) {
                        if ($idAccoteGauche == 0) {
                            $date = new \DateTime();
                            $dateInfo = $date->format('Y-m-d H:i:s');
                            $colonneInsert .= "date_information";
                            $valuesInsert .= "'$dateInfo'";
                        }
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idAccoteGauche == 0) {
                        $idAccoteGauche = $routeService->addInfoInTableByInfrastructure('t_ro_07_accotement', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneAccote) && !empty($updateColonneAccote)) {
                        $idAccoteGauche = $routeService->updateInfrastructureTables('t_ro_07_accotement', $idAccoteGauche, $updateColonneAccote);
                        }
                    } 
                    
                }

                // Accotement Droite
                $hasEtatChanged = false;
                $updateColonneAccote = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idAccoteDroite = 0;
                if (array_key_exists('accotementDroite', $data) && count($data['accotementDroite']) > 0) {
                    $hasEtatChanged = true;
                    $i = 0;
                    foreach ($data['accotementDroite'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idAccoteDroite = intval($value);
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
                            if (count($data['accotementDroite']) - 1 != $i) {
                                $updateColonneAccote .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                            } else {
                                $updateColonneAccote .= $colonne."="."$value";
                                $colonneInsert .= $colonne;
                                $valuesInsert .= $value;
                            }
                        } 
                        $i++;
                    }

                    $updateColonneAccote = trim($updateColonneAccote);
                    if (isset($updateColonneAccote[-1]) && $updateColonneAccote[-1] == ",") {
                        $updateColonneAccote = substr($updateColonneAccote, 0, strlen($updateColonneAccote) - 1);
                    }

                    if ($valuesInsert) {
                        if ($idAccoteDroite == 0) {
                            $date = new \DateTime();
                            $dateInfo = $date->format('Y-m-d H:i:s');
                            $colonneInsert .= "date_information";
                            $valuesInsert .= "'$dateInfo'";
                        }
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idAccoteDroite == 0) {
                        $idAccoteDroite = $routeService->addInfoInTableByInfrastructure('t_ro_07_accotement', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneAccote) && !empty($updateColonneAccote)) {
                        $idAccoteDroite = $routeService->updateInfrastructureTables('t_ro_07_accotement', $idAccoteDroite, $updateColonneAccote);
                        }
                    } 
                    
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
                    $hasDateInformationSituation = false;
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
                            $hasDateInformationSituation = true;
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
                        if ($idSituation == 0 && !$hasDateInformationSituation) {
                            $date = new \DateTime();
                            $dateInfo = $date->format('Y-m-d H:i:s');
                            $colonneInsert .= "date_information";
                            $valuesInsert .= "'$dateInfo'";
                        }
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idSituation == 0) {
                        $idSituation = $routeService->addInfoInTableByInfrastructure('t_ro_02_situation', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneEtat) && !empty($updateColonneEtat)) {
                        $idSituation = $routeService->updateInfrastructureTables('t_ro_02_situation', $idSituation, $updateColonneEtat);
                        }
                    } 
                    
                }

                // Surface
                $hasDataChanged = false;
                $updateColonneSurface = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idSurface = 0;
                if (array_key_exists('surface', $data) && count($data['surface']) > 0) {
                    $hasDataChanged = true;
                    $i = 0;
                    foreach ($data['surface'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idSurface = intval($value);
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
                            if (count($data['surface']) - 1 != $i) {
                                $updateColonneSurface .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                            } else {
                                $updateColonneSurface .= $colonne."="."$value";
                                $colonneInsert .= $colonne;
                                $valuesInsert .= $value;
                            }
                            
                        } 
                        $i++;
                    }

                    $updateColonneSurface = trim($updateColonneSurface);
                    if (isset($updateColonneSurface[-1]) && $updateColonneSurface[-1] == ",") {
                        $updateColonneSurface = substr($updateColonneSurface, 0, strlen($updateColonneSurface) - 1);
                    }

                    if ($valuesInsert) {
                        if ($idSurface == 0) {
                            $date = new \DateTime();
                            $dateInfo = $date->format('Y-m-d H:i:s');
                            $colonneInsert .= "date_information";
                            $valuesInsert .= "'$dateInfo'";
                        }
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idSurface == 0) {
                        $idSurface = $routeService->addInfoInTableByInfrastructure('t_ro_04_surface', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneSurface) && !empty($updateColonneSurface)) {
                        $idSurface = $routeService->updateInfrastructureTables('t_ro_04_surface', $idSurface, $updateColonneSurface);
                        }
                    }
                }


                // Structure
                $hasDataChanged = false;
                $updateColonneStructure = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idStructure = 0;
                if (array_key_exists('structure', $data) && count($data['structure']) > 0) {
                    $hasDataChanged = true;
                    $i = 0;
                    foreach ($data['structure'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idStructure = intval($value);
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
                            if (count($data['structure']) - 1 != $i) {
                                $updateColonneStructure .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                            } else {
                                $updateColonneStructure .= $colonne."="."$value";
                                $colonneInsert .= $colonne;
                                $valuesInsert .= $value;
                            }
                            
                        } 
                        $i++;
                    }

                    $updateColonneStructure = trim($updateColonneStructure);
                    if (isset($updateColonneStructure[-1]) && $updateColonneStructure[-1] == ",") {
                        $updateColonneStructure = substr($updateColonneStructure, 0, strlen($updateColonneStructure) - 1);
                    }

                    if ($valuesInsert) {
                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idStructure == 0) {
                        $idStructure = $routeService->addInfoInTableByInfrastructure('t_ro_05_structure', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneStructure) && !empty($updateColonneStructure)) {
                        $idStructure = $routeService->updateInfrastructureTables('t_ro_05_structure', $idStructure, $updateColonneStructure);
                        }
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
                        $idTravaux = $routeService->addInfoInTableByInfrastructure('t_ro_09_travaux', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneTravaux) && !empty($updateColonneTravaux)) {
                        $idTravaux = $routeService->updateInfrastructureTables('t_ro_09_travaux', $idTravaux, $updateColonneTravaux);
                        }
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
                        $idEtudes = $routeService->addInfoInTableByInfrastructure('t_ro_11_etudes', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneEtudes) && !empty($updateColonneEtudes)) {
                        $idEtudes = $routeService->updateInfrastructureTables('t_ro_11_etudes', $idEtudes, $updateColonneEtudes);
                        }
                    }
                }

                // Foncier
                $hasEtudeChanged = false;
                $updateColonneEtudes = "";
                $colonneInsert = "";
                $valuesInsert = "";
                $idFoncier = 0;
                if (array_key_exists('fonciers', $data) && count($data['fonciers']) > 0) {
                    $hasEtudeChanged = true;
                    $i = 0;
                    foreach ($data['fonciers'] as $colonne => $value) {

                        $tabColonne = explode("__", $colonne);
                        $colonne = $tabColonne[1];

                        if ($colonne == "id" || $colonne == "gid") {
                            $idFoncier = intval($value);
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
                            if (count($data['fonciers']) - 1 != $i) {
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

                    if ($idFoncier == 0) {
                        $idFoncier = $routeService->addInfoInTableByInfrastructure('t_ro_13_foncier', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneEtudes) && !empty($updateColonneEtudes)) {
                        $idFoncier = $routeService->updateInfrastructureTables('t_ro_13_foncier', $idFoncier, $updateColonneEtudes);
                        }
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
                        $idEtat = $routeService->addInfoInTableByInfrastructure('t_ro_03_etat', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneEtat) && !empty($updateColonneEtat)) {
                        $idEtat = $routeService->updateInfrastructureTables('t_ro_03_etat', $idEtat, $updateColonneEtat);
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
                        $idFourniture = $routeService->addInfoInTableByInfrastructure('t_ro_14_fourniture', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneFourniture) && !empty($updateColonneFourniture)) {
                        $idFourniture = $routeService->updateInfrastructureTables('t_ro_14_fourniture', $idFourniture, $updateColonneFourniture);
                        }
                    }
                }
            }
        
        
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Route update_successfull"
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
            //$routeService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            //$routeService->cleanTablesByIdInfrastructure($idInfra, 'etat');
            //$routeService->cleanTablesByIdInfrastructure($idInfra, 'data');
            //$routeService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            //$routeService->cleanTablesByIdInfrastructure($idInfra, 'etude');
            /*
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'surface');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'structure');
            
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'accotement');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'fosse');
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
           
            $routeService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');*/
           
        }
        
        return $response;
    }
}
