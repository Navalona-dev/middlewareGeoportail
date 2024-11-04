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
use App\Service\VoienavigableService;
use DateTime;
use Symfony\Component\HttpClient\Exception\ServerException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class VoienavigableController extends AbstractController
{
    private $pathImage = null;
    private $pathImageVoienavigable = null;
    private $pathForNameVoienavigable = null;
    private $pathPublic = null;
    private $kernelInterface;
    private $directoryCopy = null;
    private $urlGenerator;
    private const nameRepertoireImage = 'vn_voie_navigable/t_vn_01_infrastructure/';

    public function __construct(ParameterBagInterface $params, KernelInterface  $kernelInterface, UrlGeneratorInterface $urlGenerator) {
        $this->pathImage = $params->get('base_url'). $params->get('pathPublic') . self::nameRepertoireImage;
        $this->pathImageVoienavigable = $params->get('pathImageVoienavigable');
        $this->pathPublic = $params->get('pathPublic');
        $this->pathForNameVoienavigable = $params->get('pathForNameVoienavigable');
        $this->kernelInterface = $kernelInterface;
        $this->directoryCopy= $kernelInterface->getProjectDir()."/public".$params->get('pathPublic'). self::nameRepertoireImage;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/api/voienavigable/getphoto/{id}", name="infra_voienavigable_photo", methods={"GET"})
     */
    public function getPhotosByInfra($id, Request $request, VoienavigableService $voienavigableService)
    {
        $infoPhotosInfra = [];
        $response = new Response();
        if (isset($id) && !empty($id)) {
            $infoPhotosInfra = $voienavigableService->getPhotoInfraInfo($id);
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
     * @Route("/api/voienavigable/photos", name="voienavigable_get_photo", methods={"POST"})
     */
    public function getPhoto(Request $request, VoienavigableService $voienavigableService)
    {   
        $response = new Response();
        $idInfra = $request->get('infraId');

        $infoPhotosInfra = $voienavigableService->getPhotoInfraInfo($idInfra);

        $response->setContent(json_encode([
            'code'  => Response::HTTP_OK,
            'status' => true,
            'message' => "Photos Voie navigable successfull",
            'pathImage' => $this->pathImage,
            'data' => $infoPhotosInfra
        ]));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

     /**
     * @Route("/api/voienavigable/deletephoto", name="voienavigable_delete_photo", methods={"POST"})
     */
    public function deletePhoto(Request $request, VoienavigableService $voienavigableService)
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

                $infoPhotosInfra = $voienavigableService->getPhotoInfraInfo($idInfra);
                
                $oldPhotosInfra = [];
                if ($infoPhotosInfra != false && count($infoPhotosInfra) > 0 && array_key_exists($indexPhoto, $infoPhotosInfra[0])) {
                    if (isset($infoPhotosInfra[0][$indexPhoto]) && !empty($infoPhotosInfra[0][$indexPhoto]) && $infoPhotosInfra[0][$indexPhoto] != "") {
                        $oldPhotosInfra[$indexPhoto] = $infoPhotosInfra[0][$indexPhoto];
                    }
                }

                $directory = $this->pathImageVoienavigable . $indexPhoto."/";
                $directoryPublicCopy =  $this->directoryCopy. $indexPhoto."/";
                
                if (array_key_exists($indexPhoto, $oldPhotosInfra)) {
                    $nomOldFile = basename($oldPhotosInfra[$indexPhoto]);
                    if (file_exists($directory.$nomOldFile)) {
                        unlink($directory.$nomOldFile);
                        unlink($directoryPublicCopy.$nomOldFile);
                        $setUpdate .= "$indexPhoto = null, $indexPhotoName = null";
                    }
                
                    if (isset($setUpdate) && !empty($setUpdate)) {
                        $idInfra = $voienavigableService->addInfrastructurePhoto($idInfra, $setUpdate);
                    }
                   
                    $response->setContent(json_encode([
                        'code'  => Response::HTTP_OK,
                        'status' => true,
                        'message' => "Photo Voie navigable deleted_successfull"
                    ]));
                } else {
                    $response->setContent(json_encode([
                        'code'  => Response::HTTP_OK,
                        'status' => true,
                        'message' => "Pas de photo Voie navigable supprimer"
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
            /*$voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'situation');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'data');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'etude');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');*/
            /*
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'surface');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'structure');
            
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'accotement');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'fosse');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
        
            */
        
        }
        
        return $response;
    }

    /**
     * @Route("/api/voienavigable/updatephoto", name="voienavigable_update_photo", methods={"POST"})
     */
    public function updatePhoto(Request $request, VoienavigableService $voienavigableService)
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

            $infoPhotosInfra = $voienavigableService->getPhotoInfraInfo($idInfra);
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

            if(!is_dir($this->pathImageVoienavigable)) {
                mkdir($this->pathImageVoienavigable, 0777, true);
            }
          
            $directory1 = $this->pathForNameVoienavigable . "photo1/";
      
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

                $data['photo1'] = $this->pathForNameVoienavigable."photo1/" .$nomPhoto1;
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
        

            $directory2 = $this->pathForNameVoienavigable . "photo2/";

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
                
                $data['photo2'] = $this->pathForNameVoienavigable."photo2/" .$nomPhoto2;
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


            $directory3 = $this->pathForNameVoienavigable . "photo3/";
           
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

                $data['photo3'] = $this->pathForNameVoienavigable."photo3/" .$nomPhoto3;
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
                $idInfra = $voienavigableService->addInfrastructurePhoto($idInfra, $setUpdate);
            }
            

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Photo Voie navigable updated_successfull"
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
            /*$voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'situation');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'data');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'etude');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');*/
            /*
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'surface');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'structure');
            
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'accotement');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'fosse');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
        
            */
        
        }
        
        return $response;
    }

    /**
     * @Route("/api/voienavigable/addphoto", name="voienavigable_add_photo", methods={"POST"})
     */
    public function addPhoto(Request $request, VoienavigableService $voienavigableService)
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
                $directory1 = $this->pathImageVoienavigable . "photo1/";
                $directoryPublicCopy =  $this->directoryCopy. "photo1/";

                $name_temp = hash('sha512', session_id().microtime($nomOriginal1));
                $nomPhoto1 = uniqid().".".$uploadedFile1->getClientOriginalExtension();
                
                move_uploaded_file($tmpPathName1, $directory1.$nomPhoto1);
                //copy($directory1.$nomPhoto1, $directoryPublicCopy.$nomPhoto1);

                $data['photo1'] = $this->pathImageVoienavigable."photo1/" .$nomPhoto1;
                $data['photoName1'] = $nomPhoto1;
                $setUpdate .= "photo1 = '".$data['photo1']."', photo_name1 = '".$data['photoName1']."'";
            }
            
            
            if (null != $uploadedFile2) {
                $nomOriginal2 = $uploadedFile2->getClientOriginalName();
                $tmpPathName2 = $uploadedFile2->getPathname();
                $directory2 = $this->pathImageVoienavigable . "photo2/";
                $directoryPublicCopy =  $this->directoryCopy. "photo2/";

                $name_temp2 = hash('sha512', session_id().microtime($nomOriginal2));
                $nomPhoto2 = uniqid().".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName2, $directory2.$nomPhoto2);
                //copy($directory2.$nomPhoto2, $directoryPublicCopy.$nomPhoto2);
                
                $data['photo2'] = $this->pathImageVoienavigable."photo2/" .$nomPhoto2;
                $data['photoName2'] = $nomPhoto2;
                if (null != $data['photo1']) {
                    $setUpdate .= ", ";    
                }
                $setUpdate .= "photo2 = '".$data['photo2']."', photo_name2 = '".$data['photoName2']."'";
            }

            if (null != $uploadedFile3) {
                $nomOriginal3 = $uploadedFile3->getClientOriginalName();
                $tmpPathName3 = $uploadedFile3->getPathname();
                $directory3 = $this->pathImageVoienavigable . "photo3/";
                $directoryPublicCopy =  $this->directoryCopy. "photo3/";

                $name_temp3 = hash('sha512', session_id().microtime($nomOriginal3));
                $nomPhoto3 = uniqid().".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName3, $directory3.$nomPhoto3);
                //copy($directory3.$nomPhoto3, $directoryPublicCopy.$nomPhoto3);

                $data['photo3'] = $this->pathImageVoienavigable."photo3/" .$nomPhoto3;
                $data['photoName3'] = $nomPhoto3;

                if (null != $data['photo1'] || null != $data['photo2']) {
                    $setUpdate .= ", ";    
                }

                $setUpdate .= "photo3 = '".$data['photo3']."', photo_name3 = '".$data['photoName3']."'";
            }

            if (isset($setUpdate) && !empty($setUpdate)) {
                $idInfra = $voienavigableService->addInfrastructurePhoto($idInfra, $setUpdate);
            }

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Photo Voie navigable created_successfull"
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
            /*$voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'situation');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'data');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'etude');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');*/
            /*
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'surface');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'structure');
            
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'accotement');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'fosse');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
        
            */
        
        }
        
        return $response;
    }

     /**
     * @Route("/api/voienavigable/add", name="voienavigable_add", methods={"POST"})
     */
    public function create(Request $request, VoienavigableService $voienavigableService)
    {    
        $response = new Response();
        $hasException = false;
        $idInfra = null;
        try {
            $infos = json_decode($request->getContent(), true);
            $data = [];
            $data['region' ] = $infos['region'];
            $data['district' ] = $infos['district'];
            $data['communeTerrain' ] = $infos['commune'];
            $data['nom' ] = $infos['nom'];
            $data['localite'] = $infos['localite'];
        
           
            $data['localiteDepart' ] = $infos['localiteDepart'];
            $data['localiteArrivee' ] = $infos['localiteArrivee'];
            $data['sourceInformation' ] = $infos['sourceInformation'];
            $data['modeAcquisitionInformation' ] = $infos['modeAcquisitionInformation'];
          
            $data['categorie' ] = $infos['categorie'];
            $data['moisOuverture' ] = $infos['moisOuverture'];
            $data['moisFermeture' ] = $infos['moisFermeture'];
            //$data['nomRouteRattache'] = $request->get('nomRouteRattache');
            $data['nomVoieNavigableRattache'] = $infos['nomVoieNavigableRattache'];

            /*if ($request->get('nomRouteRattache') != "null" && $request->get('nomRouteRattache') != "undefined") {
                    $infoYlisteRoute = $voienavigableService->getInfoyRouteInfoMinifie($request->get('nomRouteRattache'));
                   
                    if (count($infoYlisteRoute) > 0) {
                        $data['nomRouteRattache'] = $infoYlisteRoute[0]['nom'];
                    }
            }*/

            $data['categoriePrecision'] = null;
            /*if ($request->get('categorie') != "null" && $request->get('categorie') != "undefined") {
                $allCategories = $voienavigableService->getAllCategorieInfra();
                if ($allCategories != false && count($allCategories) > 0 && !in_array($request->get('categorie'), $allCategories)) {
                        $data['categoriePrecision'] = $request->get('categorie');
                        $data['categorie' ] = "Autre à préciser";
                }
            }*/

            $data['coordonnees'] = "";
            if (count($infos['localisations']) > 0) {
                foreach ($infos['localisations'] as $key => $value) {
                    if (count($infos['localisations']) - 1 == $key) {
                        $data['coordonnees'] .= (string) $value['longitude']." ". (string) $value['latitude'];
                    } else {
                        $data['coordonnees'] .= (string) $value['longitude']." ". (string) $value['latitude'].", ";
                    }
                }
            }
            $data['indicatif'] = 'IM.H_02_04';
            
            // Situation
            $data['etat'] = $infos['situation']['etat'];
            $data['fonctionnel'] = $infos['situation']['fonctionnel'];
            $data['motif'] = $infos['situation']['motifNonFonctionel'];
            $data['sourceInformationSituation' ] = $infos['situation']['sourceInformationSituation'];
            $data['modeAcquisitionInformationSituation' ] = $infos['situation']['modeAcquisitionInformationSituation'];
            $data['raisonPrecision'] = null;

            // Data collecte
            $data['etatEnsablementCanal'] = $infos['data']['etatEnsablementCanal'];
            $data['existenceChenal'] = $infos['data']['existenceChenal'];
            $data['etatEnsablementChenal'] = $infos['data']['etatEnsablementChenal'];
            $data['existenceBalise'] = $infos['data']['existenceBalise'];
            $data['etatGlobalBalises'] = $infos['data']['etatGlobalBalises'];
            $data['existenceDigue'] = $infos['data']['existenceDigue'];
            $data['fuiteDigue'] = $infos['data']['fuiteDigue'];
            $data['existenceTrousDigue'] = $infos['data']['existenceTrousDigue'];
            $data['existenceErosionLongRechargeAval'] = $infos['data']['existenceErosionLongRechargeAval'];
            $data['existenceOuvrageAccostage'] = $infos['data']['existenceOuvrageAccostage'];
            $data['typeOuvrageAccostage'] = $infos['data']['typeOuvrageAccostage'];
            $data['existenceFissureOuvrageBeton'] = $infos['data']['existenceFissureOuvrageBeton'];
            $data['existenceFerraillageVisiblePourOuvrageBeton'] = $infos['data']['existenceFerraillageVisiblePourOuvrageBeton'];
            $data['existenceFissurePourOuvrageMaconnerie'] = $infos['data']['existenceFissurePourOuvrageMaconnerie'];
            $data['existencePouillePourOuvrageFer'] = $infos['data']['existencePouillePourOuvrageFer'];
            $data['sourceInformationData'] = $infos['data']['sourceInformationData'];
            $data['modeAcquisitionInformationData' ] = $infos['data']['modeAcquisitionInformationData'];
          

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
            
            
            $data['photo1'] = null;
            $data['photo2'] = null;
            $data['photo3'] = null;
            $data['photoName1'] = null;
            $data['photoName2'] = null;
            $data['photoName3'] = null;

            $idInfra = $voienavigableService->addInfrastructure($data);

            if ($idInfra != false) {
                // add situation et etat
                //$idEtat = $voienavigableService->addInfrastructureRouteEtat($idInfra, $data);

                $idEtat = $voienavigableService->addInfrastructureSituation($idInfra, $data);

                $idDataCollected = $voienavigableService->addInfrastructureDonneCollecte($idInfra, $data);

                /*$idStructure = $voienavigableService->addInfrastructureRouteStructure($idInfra, $data);

                $idAccotement = $voienavigableService->addInfrastructureRouteAccotement($idInfra, $data);

                $idFosse = $voienavigableService->addInfrastructureRouteFosse($idInfra, $data);*/
            

                /**
                 * Administrative data
                 */
                //Foncier
                /*$data['statut'] = $request->get('statutFoncier');
                $data['numeroReference'] = $request->get('numeroReferenceFoncier');
                $data['nomProprietaire'] = $request->get('nomProprietaireFoncier');

                $idFoncier = $voienavigableService->addInfrastructureRouteFoncier($idInfra, $data);*/

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

                    $idTravaux = $voienavigableService->addInfrastructureTravaux($idInfra, $data);
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
                $idFourniture = $voienavigableService->addInfrastructureRouteFourniture($idInfra, $data);*/
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
                    $idEtude = $voienavigableService->addInfrastructureEtudes($idInfra, $data);
                }
                
                /**
                 * End Administrative data
                */
                //$idDonneAnnexe = $voienavigableService->addInfrastructureEducationDonneAnnexe($idInfra, $data);
            }

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "voienavigable route created_successfull"
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
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'situation');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'data');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'etude');
            /*
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'surface');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'structure');
            
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'accotement');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'fosse');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
           
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');*/
           
        }
        
        return $response;
    }
    
    /**
     * @Route("/api/infra/voienavigable/liste", name="voienavigable_list", methods={"GET"})
     */
    public function listeTrajeRoute(Request $request, VoienavigableService $voienavigableService)
    {    
        $response = new Response();
        
        try {

            $routes = $voienavigableService->getAllInfrastructures();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Voie navigable list_successfull",
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
     * @Route("/api/infra/voienavigable/liste/minifie", name="voienavigable_list_minifie", methods={"GET"})
     */
    public function listevoienavigableMinifie(Request $request, VoienavigableService $voienavigableService)
    {    
        $response = new Response();
        
        try {

            $routes = $voienavigableService->getAllInfrastructuresMinifie();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Voie navigable list_successfull",
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
     * @Route("/api/infra/voienavigable/info", name="voienavigable_info", methods={"POST"})
     */
    public function getOneInfraInfo(Request $request, VoienavigableService $voienavigableService)
    {    
        $response = new Response();
        
        try {
            $infraId = $request->get('id');

            $routes = $voienavigableService->getOneInfraInfo(intval($infraId));

            /*$routesInfrastructure = $voienavigableService->getAllyRouteInfoMinifie();
            $infoRoutes = [];
            if ($routes != false && count($routes) > 0 && $routesInfrastructure != false && count($routesInfrastructure) > 0 ) {
                foreach ($routesInfrastructure as $key => $value) {
                   if (trim($value['nom']) == trim($routes[0]['nom_de_la_route_a_qui_il_est_rattache'])) {
                    $infoRoutes = $value;
                   }
                }
            
            }
            
            if ($routes != false && count($routes) > 0) {
                $routes[0]['infoRoutes'] = false;
                if ($infoRoutes != false) {
                    $routes[0]['infoRoutes'] = $infoRoutes;
                }
            }*/

            //dd($this->urlGenerator->generate('images_route', ['imageName' => '64b1501d625a7.jpg']));
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Voie navigable infrastructure successfull",
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
     * @Route("/api/voienavigable/update", name="voienavigable_update", methods={"POST"})
     */
    public function update(Request $request, VoienavigableService $voienavigableService)
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
                $colonneFloat = ['duree_theorique_de_la_traversee', 'duree_reelle_de_la_traversee', 'longueur', 'largeur', 'charge_maximum', 'Largeur_chaussée', 'Largeur_accotements', 'decalage_de_la_jointure_du_tablier_chaussee_en_affaissement', 'decalage_de_la_jointure_du_tablier_chaussee_en_ecartement'];

                $colonneDate = ["date_infromation", "date_information", "date_contrat", "date_ordre_service", "date_reception_provisoire", "date_reception_definitive"];
                
                if (array_key_exists('infrastructure', $data) && count($data['infrastructure']) > 0) {
                    $hasInfraChanged = true;
                    $i = 0;

                    if (array_key_exists("localisations", $data['infrastructure'])) {
                        $coordonnees = "";
                        if (count($data['infrastructure']['localisations']) > 0) {
                            
                            foreach ($data['infrastructure']['localisations'] as $key => $value) {
                                if (count($data['infrastructure']['localisations']) - 1 == $key) {
                                    $coordonnees .= (string) $value['longitude']." ". (string) $value['latitude'];
                                } else {
                                    $coordonnees .= (string) $value['longitude']." ". (string) $value['latitude'].", ";
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
                                /*if ($colonne == "nom_de_la_route_a_qui_il_est_rattache") {
                                    if ($value != "null" && $value != "undefined" && $value != "") {
                                        $infoYlisteRoute = $voienavigableService->getInfoyRouteInfoMinifie($value);
                                        if ($infoYlisteRoute != false && count($infoYlisteRoute) > 0) {
                                            $value = $infoYlisteRoute[0]['nom'];
                                            $value = pg_escape_string($value);
                                            $value = "'$value'";
                                        }
                                    }
                                } else {*/
                                    $value = pg_escape_string($value);
                                    $value = "'$value'";
                                //}
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
                   
                    $idInfra = $voienavigableService->updateInfrastructure($idInfra, $updateColonneInfra);
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
                                if ($colonne == 'source_information') {
                                    $date = new \DateTime();
                                    $dateInfo = $date->format('Y-m-d H:i:s');
                                    $colonneInsert .= "date_information, ";
                                    $valuesInsert .= "'$dateInfo', ";
                                }

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
                            $colonneInsert .= ", date_information";
                            $valuesInsert .= "'$dateInfo'";
                        }
                        

                        $valuesInsert = trim($valuesInsert);
                        if ($valuesInsert[-1] && $valuesInsert[-1] == ",") {
                            $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert) - 1);
                        }
                    }

                    if ($idSituation == 0) {
                        $idSituation = $voienavigableService->addInfoInTableByInfrastructure('t_vn_02_situation', $colonneInsert, $valuesInsert);
                    } else {
                        $idSituation = $voienavigableService->updateInfrastructureTables('t_vn_02_situation', $idSituation, $updateColonneEtat);
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
                                if ($colonne == 'source_information') {
                                    $date = new \DateTime();
                                    $dateInfo = $date->format('Y-m-d H:i:s');
                                    $colonneInsert .= "date_information, ";
                                    $valuesInsert .= "'$dateInfo', ";
                                }
                                $updateColonneData .= $colonne."="."$value".", ";
                                $colonneInsert .= $colonne.", ";
                                $valuesInsert .= $value.", ";
                                    
                                
                            } else {
                                if ($colonne == 'source_information') {
                                    $date = new \DateTime();
                                    $dateInfo = $date->format('Y-m-d H:i:s');
                                    $colonneInsert .= ", date_information";
                                    $valuesInsert .= "'$dateInfo'";
                                }

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

                   // dd($colonneInsert, $valuesInsert);
                    if ($idData == 0) {
                        $idData = $voienavigableService->addInfoInTableByInfrastructure('t_vn_04_donnees_collectees', $colonneInsert, $valuesInsert);
                    } else {
                        $idData = $voienavigableService->updateInfrastructureTables('t_vn_04_donnees_collectees', $idData, $updateColonneData);
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
                        $idTravaux = $voienavigableService->addInfoInTableByInfrastructure('t_vn_05_travaux', $colonneInsert, $valuesInsert);
                    } else {
                        $idTravaux = $voienavigableService->updateInfrastructureTables('t_vn_05_travaux', $idTravaux, $updateColonneTravaux);
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
                        $idEtudes = $voienavigableService->addInfoInTableByInfrastructure('t_vn_07_etudes', $colonneInsert, $valuesInsert);
                    } else {
                        $idEtudes = $voienavigableService->updateInfrastructureTables('t_vn_07_etudes', $idEtudes, $updateColonneEtudes);
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
                        $idEtat = $voienavigableService->addInfoInTableByInfrastructure('t_vn_03_etat', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneEtat) && !empty($updateColonneEtat)) {
                        $idEtat = $voienavigableService->updateInfrastructureTables('t_vn_03_etat', $idEtat, $updateColonneEtat);
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
                        $idFourniture = $voienavigableService->addInfoInTableByInfrastructure('t_vn_06_fourniture', $colonneInsert, $valuesInsert);
                    } else {
                        if (isset($updateColonneFourniture) && !empty($updateColonneFourniture)) {
                        $idFourniture = $voienavigableService->updateInfrastructureTables('t_vn_06_fourniture', $idFourniture, $updateColonneFourniture);
                        }
                    }
                }
            }
        
        
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Voie navigable update_successfull"
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
            //$voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            //$voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'etat');
            //$voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'data');
            //$voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            //$voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'etude');
            /*
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'surface');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'structure');
            
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'accotement');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'fosse');
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
           
            $voienavigableService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');*/
           
        }
        
        return $response;
    }
}
