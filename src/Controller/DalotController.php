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
use App\Service\DalotService;


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

class DalotController extends AbstractController
{
    private $pathImage = null;
    private $pathImageDalot = null;
    private $pathPublic = null;
    private $pathForNamePhotoDalot = null;
    private $kernelInterface;
    private $directoryCopy = null;
    private const nameRepertoireImage = 'dar_dalot_route/t_dar_01_infrastructure/';

    public function __construct(ParameterBagInterface $params, KernelInterface  $kernelInterface) {
        $this->pathImage = $params->get('base_url'). $params->get('pathPublic') . self::nameRepertoireImage;
        $this->pathImageDalot = $params->get('pathImageDalot');
        $this->pathPublic = $params->get('pathPublic');
        $this->pathForNamePhotoDalot = $params->get('pathForNamePhotoDalot');
        $this->kernelInterface = $kernelInterface;
        $this->directoryCopy= $kernelInterface->getProjectDir()."/public".$params->get('pathPublic'). self::nameRepertoireImage;
    }

    /**
     * @Route("/api/dalotroute/getphoto/{id}", name="infra_dalotroute_photo", methods={"GET"})
     */
    public function getPhotosByInfra($id, Request $request, DalotService $dalotService)
    {
        $infoPhotosInfra = [];
        $response = new Response();
        if (isset($id) && !empty($id)) {
            $infoPhotosInfra = $dalotService->getPhotoInfraInfo($id);
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
     * @Route("/api/dalotroute/deletephoto", name="dalotroute_delete_photo", methods={"POST"})
     */
    public function deletePhoto(Request $request, DalotService $dalotService)
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

                $infoPhotosInfra = $dalotService->getPhotoInfraInfo($idInfra);
                
                $oldPhotosInfra = [];
                if ($infoPhotosInfra != false && count($infoPhotosInfra) > 0 && array_key_exists($indexPhoto, $infoPhotosInfra[0])) {
                    if (isset($infoPhotosInfra[0][$indexPhoto]) && !empty($infoPhotosInfra[0][$indexPhoto]) && $infoPhotosInfra[0][$indexPhoto] != "") {
                        $oldPhotosInfra[$indexPhoto] = $infoPhotosInfra[0][$indexPhoto];
                    }
                }

                $directory = $this->pathImageDalot . $indexPhoto."/";
                $directoryPublicCopy =  $this->directoryCopy. $indexPhoto."/";
                
                if (array_key_exists($indexPhoto, $oldPhotosInfra)) {
                    $nomOldFile = basename($oldPhotosInfra[$indexPhoto]);
                    if (file_exists($directory.$nomOldFile)) {
                        unlink($directory.$nomOldFile);
                        unlink($directoryPublicCopy.$nomOldFile);
                        $setUpdate .= "$indexPhoto = null, $indexPhotoName = null";
                    }
                
                    if (isset($setUpdate) && !empty($setUpdate)) {
                        $idInfra = $dalotService->addInfrastructurePhoto($idInfra, $setUpdate);
                    }
                   
                    $response->setContent(json_encode([
                        'code'  => Response::HTTP_OK,
                        'status' => true,
                        'message' => "Photo dalot route deleted_successfull"
                    ]));
                } else {
                    $response->setContent(json_encode([
                        'code'  => Response::HTTP_OK,
                        'status' => true,
                        'message' => "Pas de photo dalot route supprimer"
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
     * @Route("/api/dalotroute/updatephoto", name="dalotroute_update_photo", methods={"POST"})
     */
    public function updatePhoto(Request $request, DalotService $dalotService)
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

            $infoPhotosInfra = $dalotService->getPhotoInfraInfo($idInfra);
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

            if(!is_dir($this->pathImageDalot)) {
                mkdir($this->pathImageDalot, 0777, true);
            }
          
            $directory1 = $this->pathImageDalot . "photo1/";
      
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

                $data['photo1'] = $this->pathForNamePhotoDalot."photo1/" .$nomPhoto1;
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
        

            $directory2 = $this->pathImageDalot . "photo2/";

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
                
                $data['photo2'] = $this->pathForNamePhotoDalot."photo2/" .$nomPhoto2;
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


            $directory3 = $this->pathImageDalot . "photo3/";
           
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

                $data['photo3'] = $this->pathForNamePhotoDalot."photo3/" .$nomPhoto3;
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
                $idInfra = $dalotService->addInfrastructurePhoto($idInfra, $setUpdate);
            }
            

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Photo dalot route updated_successfull"
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
     * @Route("/api/dalot/add", name="dalot_add", methods={"POST"})
     */
    public function create(Request $request, DalotService $dalotService)
    {    
        $response = new Response();
        $hasException = false;
        $idInfra = null;
        try {

            $data = [];
            $data['region' ] = $request->get('region');
            $data['district' ] = $request->get('district');
            $data['communeTerrain' ] = $request->get('commune');
            //$data['nom' ] = $request->get('nom');

            $data['nom'] = null;

            if ($request->get('nom') != "null" && $request->get('nom') != "undefined") {
                    $infoYlisteRoute = $dalotService->getInfoyRouteInfoMinifie($request->get('nom'));
                   
                    if ($infoYlisteRoute != false && count($infoYlisteRoute) > 0) {
                        $data['nom'] = $infoYlisteRoute[0]['nom'];
                    }
            }

            $data['localite'] = null;

            if ($request->get('localite') != "null" && $request->get('localite') != "undefined") {
                $data['localite'] = $request->get('localite');
            }

            $data['type' ] = $request->get('typeDalot');
            $data['sourceInformation' ] = $request->get('sourceInformation');
            $data['modeAcquisitionInformation' ] = $request->get('modeAcquisitionInformation');
            $data['pkImplantation' ] = $request->get('pkImplantation');
            $data['materiauxRadier'] = null;

            if ($request->get('materiauxRadier') != "null" && $request->get('materiauxRadier') != "undefined") {
                $data['materiauxRadier'] = $request->get('materiauxRadier');
            }
            $data['precisionMateriauxRadier'] = null;

            if ($request->get('precisionMateriauxRadier') != "null" && $request->get('precisionMateriauxRadier') != "undefined") {
                $data['precisionMateriauxRadier'] = $request->get('precisionMateriauxRadier');
            }
            $data['materiauxPiedroit'] = null;

            if ($request->get('materiauxPiedroit') != "null" && $request->get('materiauxPiedroit') != "undefined") {
                $data['materiauxPiedroit'] = $request->get('materiauxPiedroit');
            }
            $data['materiauxDalle'] = null;

            if ($request->get('materiauxDalle') != "null" && $request->get('materiauxDalle') != "undefined") {
                $data['materiauxDalle'] = $request->get('materiauxDalle');
            }
            
            $data['precisionMateriauxDalle'] = null;

            if ($request->get('materiauxDalle') != "null" && $request->get('precisionMateriauxDalle') != "undefined") {
                $data['precisionMateriauxDalle'] = $request->get('precisionMateriauxDalle');
            }

            $data['precisionMateriauxPiedroit'] = null;

            if ($request->get('precisionMateriauxPiedroit') != "null" && $request->get('precisionMateriauxPiedroit') != "undefined") {
                $data['precisionMateriauxPiedroit'] = $request->get('precisionMateriauxPiedroit');
            }

            $data['latitude'] = $request->get('latitude');
            $data['longitude'] = $request->get('longitude');

            // Etat
            $data['etat'] = $request->get('etat');
            $data['fonctionnel'] = $request->get('fonctionnel');
            $data['motif'] = $request->get('motifNonFonctionel');
            $data['sourceInformationEtat' ] = $request->get('sourceInformationEtat');
            $data['modeAcquisitionInformationEtat' ] = $request->get('modeAcquisitionInformationEtat');
            
            // Data collecte
            $data['existenceFissures'] = null;
            if ($request->get('existenceFissures') != "null" && $request->get('existenceFissures') != "undefined") {
                $data['existenceFissures'] = $request->get('existenceFissures');
            }
            
            $data['niveauEnsablementOuverture'] = null;
            if ($request->get('niveauEnsablementOuverture') != "null" && $request->get('niveauEnsablementOuverture') != "undefined") {
                $data['niveauEnsablementOuverture'] = $request->get('niveauEnsablementOuverture');
            }

            $data['sourceInformationData'] = $request->get('sourceInformationData');
            $data['modeAcquisitionInformationData' ] = $request->get('modeAcquisitionInformationData');
            $data['autreDegradation' ] = $request->get('autreDegradation');
            
            /* $data['structure'] = $request->get('structure');
            $data['procedureTravaux'] = $request->get('procedureTravaux');
            $data['precisionStructure'] = $request->get('precisionStructure');
            $data['precisionModeGestion'] = $request->get('precisionModeGestion');
           
            
            
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
            //$data['accotementHasAccotementGauche'] = $request->get('accotementHasAccotementGauche');
            $data['accotement'] = $request->get('accotement');
            //$data['accotementIsAccotementNonRevetu'] = $request->get('accotementIsAccotementNonRevetu');
            $data['accotementRevetue'] = $request->get('accotementRevetue');
            $data['accotementTypeRevetementAccotement'] = $request->get('accotementTypeRevetementAccotement');
            $data['accotementDegrationSurface'] = $request->get('accotementDegrationSurface');
            $data['accotementDentelleRive'] = $request->get('accotementDentelleRive');
            $data['accotementPrecisionTypeAccotement'] = $request->get('accotementPrecisionTypeAccotement');

            $data['accotementDenivellationChausseAccotement'] = $request->get('accotementDenivellationChausseAccotement');
            $data['accotementDestructionAffouillementAccotement'] = $request->get('accotementDestructionAffouillementAccotement');
            $data['accotementNonRevetueDeformationProfil'] = $request->get('accotementNonRevetueDeformationProfil');

            $dateInformationAccotement = new \DateTime($request->get('dateInformationAccotement'));
            $dateInformationAccotement->format('Y-m-d H:i:s');
            $data['dateInformationAccotement'] = $dateInformationAccotement;
            $data['sourceInformationAccotement' ] = $request->get('sourceInformationAccotement');
            $data['modeAcquisitionInformationAccotement' ] = $request->get('modeAcquisitionInformationAccotement');

            $data['fosseRevetu'] = $request->get('fosseRevetu');
            $data['fosseRevetuDegradationFosse'] = $request->get('fosseRevetuDegradationFosse');
            $data['fosseRevetuSectionBouche'] = $request->get('fosseRevetuSectionBouche');
            $data['fosseNonRevetuFosseProfil'] = $request->get('fosseNonRevetuFosseProfil');
            $data['fosseNonRevetuEncombrement'] = $request->get('fosseNonRevetuEncombrement');

            $dateInformationFosse = new \DateTime($request->get('dateInformationFosse'));
            $dateInformationFosse->format('Y-m-d H:i:s');
            $data['dateInformationFosse'] = $dateInformationFosse;
            $data['sourceInformationFosse' ] = $request->get('sourceInformationFosse');
            $data['modeAcquisitionInformationFosse' ] = $request->get('modeAcquisitionInformationFosse');
            $data['coteFosse'] = $request->get('coteFosse');*/
            
            
            $uploadedFile1 = $request->files->get('photo1');
            $uploadedFile2 = $request->files->get('photo2');
            $uploadedFile3 = $request->files->get('photo3');
            $data['photo1'] = null;
            $data['photo2'] = null;
            $data['photo3'] = null;
            $data['photoName1'] = null;
            $data['photoName2'] = null;
            $data['photoName3'] = null;

            if(!is_dir($this->pathImageDalot)) {
                mkdir($this->pathImageDalot, 0777, true);
            }

            if (null != $uploadedFile1) {
                $nomOriginal1 = $uploadedFile1->getClientOriginalName();
                $tmpPathName1 = $uploadedFile1->getPathname();
                $directory1 = $this->pathImageDalot . "photo1/";
                
                $directoryPublicCopy =  $this->directoryCopy. "photo1/";

                if(!is_dir($directory1)) {
                    mkdir($directory1, 0777, true);
                }

                if(!is_dir($directoryPublicCopy)) {
                    mkdir($directoryPublicCopy, 0777, true);
                } 

                $name_temp = hash('sha512', session_id().microtime($nomOriginal1));
                $nomPhoto1 = uniqid().".".$uploadedFile1->getClientOriginalExtension();
                
                move_uploaded_file($tmpPathName1, $directory1.$nomPhoto1);
                //copy($directory1.$nomPhoto1, $directoryPublicCopy.$nomPhoto1);

                $data['photo1'] = $this->pathForNamePhotoDalot."photo1/" .$nomPhoto1;
                $data['photoName1'] = $nomPhoto1;
            }
            
            if (null != $uploadedFile2) {
                $nomOriginal2 = $uploadedFile2->getClientOriginalName();
                $tmpPathName2 = $uploadedFile2->getPathname();
                $directory2 = $this->pathImageDalot . "photo2/";
                
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
                
                $data['photo2'] = $this->pathForNamePhotoDalot."photo2/" .$nomPhoto2;
                $data['photoName2'] = $nomPhoto2;
            }

            if (null != $uploadedFile3) {
                $nomOriginal3 = $uploadedFile3->getClientOriginalName();
                $tmpPathName3 = $uploadedFile3->getPathname();
                $directory3 = $this->pathImageDalot . "photo3/";
                
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

                $data['photo3'] = $this->pathForNamePhotoDalot."photo3/" .$nomPhoto3;
                $data['photoName3'] = $nomPhoto3;
            }

            $idInfra = $dalotService->addInfrastructure($data);

            if ($idInfra != false) {
                // add situation et etat
                //$idEtat = $dalotService->addInfrastructureRouteEtat($idInfra, $data);

                $idEtat = $dalotService->addInfrastructureEtat($idInfra, $data);

                $idDataCollected = $dalotService->addInfrastructureDonneCollecte($idInfra, $data);

                /*$idStructure = $dalotService->addInfrastructureRouteStructure($idInfra, $data);

                $idAccotement = $dalotService->addInfrastructureRouteAccotement($idInfra, $data);
                $idFosse = $dalotService->addInfrastructureRouteFosse($idInfra, $data);*/
            

                /**
                 * Administrative data
                 */
                //Foncier
                /*$data['statut'] = $request->get('statutFoncier');
                $data['numeroReference'] = $request->get('numeroReferenceFoncier');
                $data['nomProprietaire'] = $request->get('nomProprietaireFoncier');

                $idFoncier = $dalotService->addInfrastructureRouteFoncier($idInfra, $data);*/

                //Travaux 
                if (null != $request->get('hasTravaux') && ($request->get('hasTravaux') == true || $request->get('hasTravaux') == "true") && "false" != $request->get('hasTravaux')) {
                    $data['objetTravaux'] = $request->get('objetTravaux');
                    $data['consistanceTravaux'] = $request->get('consistanceTravaux');
                    //$data['modeRealisationTravaux'] = $request->get('modeRealisationTravaux');
                    $data['maitreOuvrageTravaux'] = $request->get('maitreOuvrageTravaux');
                    $data['maitreOeuvreTravaux'] = $request->get('maitreOeuvreTravaux');
                    $data['maitreOuvrageDelegueTravaux'] = $request->get('maitreOuvrageDelegueTravaux');
                    $data['idControleSurveillanceTravaux'] = $request->get('idControleSurveillanceTravaux');//idControleSurveillance
                    $data['modePassationTravaux'] = $request->get('modePassationTravaux');
                    $data['porteAppelOffreTravaux'] = $request->get('porteAppelOffreTravaux');
                    $data['montantTravaux'] = $request->get('montantTravaux');
                    $data['numeroContratTravaux'] = $request->get('numeroContratTravaux');
                    //$data['precisionConsistanceTravaux'] = $request->get('precisionConsistanceTravaux');
                    
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

                    $idTravaux = $dalotService->addInfrastructureTravaux($idInfra, $data);
                }
                
                // Fournitures
                /*$data['objetContratFourniture'] = $request->get('objetContratFourniture');
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
                $idFourniture = $dalotService->addInfrastructureRouteFourniture($idInfra, $data);*/
                // Etudes
                if (null != $request->get('hasEtude') && ($request->get('hasEtude') == true || $request->get('hasEtude') == "true") && "false" != $request->get('hasEtude')) {
                    $data['objetContratEtude'] = $request->get('objetContratEtude');
                    $data['consistanceContratEtude'] = $request->get('consistanceContratEtude');
                    $data['entiteEtude'] = $request->get('entiteEtude');
                    $data['idTitulaireEtude'] = $request->get('idTitulaireEtude');
                    $data['montantContratEtude'] = $request->get('montantContratEtude');
                    $data['numeroContratEtude'] = $request->get('numeroContratEtude');
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
                // $data['precisionConsistanceContratEtude'] = $request->get('precisionConsistanceContratEtude');
                    $data['bailleurEtude'] = $request->get('bailleurEtude');
                    $idEtude = $dalotService->addInfrastructureEtudes($idInfra, $data);
                }
                
                /**
                 * End Administrative data
                */
                //$idDonneAnnexe = $dalotService->addInfrastructureEducationDonneAnnexe($idInfra, $data);
            }

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Dalot route created_successfull"
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
            $dalotService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            $dalotService->cleanTablesByIdInfrastructure($idInfra, 'etat');
            $dalotService->cleanTablesByIdInfrastructure($idInfra, 'data');
            $dalotService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            $dalotService->cleanTablesByIdInfrastructure($idInfra, 'etude');
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

    /**
     * @Route("/api/infra/dalot/info", name="dalot_info", methods={"POST"})
     */
    public function getOneInfraInfo(Request $request, DalotService $dalotService)
    {    
        $response = new Response();
        
        try {
            $infraId = $request->get('id');

            $routes = $dalotService->getOneInfraInfo(intval($infraId));

            $routesInfrastructure = $dalotService->getAllyRouteInfoMinifie();
            $infoRoutes = [];
            if ($routes != false && count($routes) > 0 && $routesInfrastructure != false && count($routesInfrastructure) > 0 ) {
                foreach ($routesInfrastructure as $key => $value) {
                   if (trim($value['nom']) == trim($routes[0]['nom'])) {
                    $infoRoutes = $value;
                   }
                }
            
            }
            
            if ($routes != false && count($routes) > 0) {
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
     * @Route("/api/infra/dalot/liste", name="dalot_list", methods={"GET"})
     */
    public function listeDalot(Request $request, DalotService $dalotService)
    {    
        $response = new Response();
        
        try {

            $routes = $dalotService->getAllInfrastructures();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "dalot route list_successfull",
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
     * @Route("/api/infra/dalot/liste/minifie", name="dalot_list_minifie", methods={"GET"})
     */
    public function listeDalotMinifie(Request $request, DalotService $dalotService)
    {    
        $response = new Response();
        
        try {

            $routes = $dalotService->getAllInfrastructuresMinifie();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "dalot route list_successfull",
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
     * @Route("/api/dalot/update", name="dalot_update", methods={"POST"})
     */
    public function update(Request $request, DalotService $dalotService)
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
                            if ($colonne == "nom") {
                                if ($value != "null" && $value != "undefined" && $value != "") {
                                    $infoYlisteRoute = $dalotService->getInfoyRouteInfoMinifie($value);
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

                    if (isset($updateColonneInfra) && !empty($updateColonneInfra)) {
                        $idInfra = $dalotService->updateInfrastructure($idInfra, $updateColonneInfra);
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
                    $hasDateInformationEtat = false;
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
                            $hasDateInformationEtat = true;
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
                        if ($idEtat == 0 && !$hasDateInformationEtat) {
                            $date = new \DateTime();
                            $dateInfo = $date->format('Y-m-d H:i:s');
                            $colonneInsert .= "date_information";
                            $valuesInsert .= "'$dateInfo'";
                        }
                        $valuesInsert = trim($valuesInsert);
                        if (isset($valuesInsert[-1]) && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idEtat == 0) {
                        $idEtat = $dalotService->addInfoInTableByInfrastructure('t_dar_03_etat', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneEtat) && !empty($updateColonneEtat)) {
                            $idEtat = $dalotService->updateInfrastructureTables('t_dar_03_etat', $idEtat, $updateColonneEtat);
                        }
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
                    $hasDateInformationData = false;
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

                            $hasDateInformationData = true;
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
                        if ($idData == 0 && !$hasDateInformationData) {
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

                    if ($idData == 0) {
                        $idData = $dalotService->addInfoInTableByInfrastructure('t_dar_04_donnees_collectees', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneData) && !empty($updateColonneData)) {
                        $idData = $dalotService->updateInfrastructureTables('t_dar_04_donnees_collectees', $idData, $updateColonneData);
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
                        if ($idTravaux == 0) {
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

                    if ($idTravaux == 0) {
                        $idTravaux = $dalotService->addInfoInTableByInfrastructure('t_dar_05_travaux', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneTravaux) && !empty($updateColonneTravaux)) {
                        $idTravaux = $dalotService->updateInfrastructureTables('t_dar_05_travaux', $idTravaux, $updateColonneTravaux);
                        }
                    }
                }

                // Travaux
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
                        $idEtudes = $dalotService->addInfoInTableByInfrastructure('t_dar_07_etudes', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneEtudes) && !empty($updateColonneEtudes)) {
                        $idEtudes = $dalotService->updateInfrastructureTables('t_dar_07_etudes', $idEtudes, $updateColonneEtudes);
                        }
                    }
                }
            }
        
        
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Dalot route update_successfull"
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
