<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\LocalisationInfrastructureService;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\ORMInvalidArgumentException;
use App\Exception\PropertyVideException;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Exception\UnsufficientPrivilegeException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class LocalisationInfrastructureController extends AbstractController
{
    /**
     * @Route("/api/regions", name="all_regions", methods={"GET"})
     */
    public function listeRegionsInfrastructure(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        $response = new Response();

        try {

            $regionsInfrastructure = $localisationInfrastructureService->getAllRegions();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Regions list_successfull",
                'data' => $regionsInfrastructure
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
     * @Route("/api/districts", name="districts_region", methods={"POST"})
     */
    public function listeDistrictsByRegion(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        $response = new Response();

        try {

            $data = json_decode($request->getContent(), true);
            $region = $data['region'];
            $commmunesInfrastructure = $localisationInfrastructureService->getAllDistrictByRegion($region);
            
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Districts list_successfull",
                'data' => $commmunesInfrastructure
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
     * @Route("/api/communes", name="commmunes_district_region", methods={"POST"})
     */
    public function listeCommunesByDistrictInRegion(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        
        $response = new Response();

        try {

            $data = json_decode($request->getContent(), true);
            $region = $data['region'];
            $district = $data['district'];
            $commmunesInfrastructure = $localisationInfrastructureService->getAllCommunesByDistrictInRegion($region, $district);
            
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "communes list_successfull",
                'data' => $commmunesInfrastructure
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
     * @Route("/api/localites/liste", name="locatite_liste_commmunes_region", methods={"GET"})
     */
    public function listeLocalite(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        
        $response = new Response();

        try {

            $data = json_decode($request->getContent(), true);
           
            $localitesInfrastructure = $localisationInfrastructureService->getAllCoordonneLocalites();
            
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "localites list_successfull",
                'data' => $localitesInfrastructure
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
     * @Route("/api/localites", name="locatite_commmunes_region", methods={"POST"})
     */
    public function listeLocaliteInCommunesInRegion(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        
        $response = new Response();

        try {

            $data = json_decode($request->getContent(), true);
            $codeCommune = $data['codeCommune'];
            $localitesInfrastructure = $localisationInfrastructureService->getAllLocalitesInCommunes($codeCommune);
            
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "localites list_successfull",
                'data' => $localitesInfrastructure
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
     * @Route("/api/localisation/infrastructure", name="localisation_infrastructure", methods={"GET"})
     */
    public function getLocalisationInfrastructure(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        $response = new Response();

        try {
            $data = [];
            
            $regionsInfrastructure = $localisationInfrastructureService->getAllRegions();
            
            $districtsInfrastructure = $localisationInfrastructureService->getAllDistricts();

            $communesInfrastructure = $localisationInfrastructureService->getAllCommunes();

            $localitesInfrastructure = $localisationInfrastructureService->getAllLocalitesInstat();
          
            ini_set('memory_limit','2G');
            set_time_limit(0);
            $tabLocalisation = [];
            if (count($regionsInfrastructure) > 0) {
                foreach($regionsInfrastructure as $region) {
                    $unRegion = [];
                    $unDistrict = [];
                    $unCommune = [];
                    $unRegion['region'] = $region['region'];
                    $unRegion['reg_ceni'] = $region['reg_ceni'];
                    $unRegion['districts'] = [];
                    $unRegionFilter = [];
                    $unCommuneFilter = [];
                    $unCommuneFilter['communesFilter'] = [];
                    $unCommuneFilter['communesFilter']['communes'] = [];
                    $unRegionFilter['districtsFilter'] = [];
                    $unRegionFilter['districtsFilter']['communes'] = [];

                    if (count($districtsInfrastructure) > 0) {
                        $unRegionFilter['districtsFilter']  = array_filter($districtsInfrastructure, function ($district) use ($region) {
                            return $district['reg_ceni'] === $region['reg_ceni'];
                        });

                        if (count($unRegionFilter['districtsFilter']) > 0) {
                            foreach($unRegionFilter['districtsFilter'] as $district) {
                                $unDistrict['district'] = $district['district'];
                                $unDistrict['dist_ceni'] = $district['dist_ceni'];
                                $unDistrict['reg_ceni'] = $district['reg_ceni'];
                                $unDistrict['communes'] = [];

                                $tabCommunes = array_filter($communesInfrastructure, function ($commune) use ($district) {
                                    return $district['dist_ceni'] === $commune['dist_ceni'];
                                });
                          
                                if (count($tabCommunes) > 0) {
                                    foreach($tabCommunes as $commune) {
                                        $unCommune['commune'] = $commune['commune'];
                                        $unCommune['com_ceni'] = $commune['com_ceni'];
                                        $unCommune['reg_ceni'] = $commune['reg_ceni'];
                                        $unCommune['dist_ceni'] = $commune['dist_ceni'];
                                        $unCommune['localites'] = [];
                                        $tabLocalites = array_filter($localitesInfrastructure, function ($localite) use ($commune) {
                                            return strtoupper($localite['commune']) === strtoupper($commune['commune']);
                                        });
                                       
                                        if (count($tabLocalites) > 0) {
                                            foreach($tabLocalites as $localite) {
                                                //$unLocalite['district'] = $localite['district'];
                                                //$unLocalite['commune'] = $localite['commune'];
                                                $unLocalite['com_ceni'] = $localite['commune'];
                                                $unLocalite['localite'] = $localite['localite'];
                                                $unLocalite['latitude'] = $localite['lat'];
                                                $unLocalite['longitude'] = $localite['long'];
                                                array_push($unCommune['localites'], $unLocalite);
                                            }
                                        }
                                        array_push($unDistrict['communes'], $unCommune);
                                    }
                                    $unRegion['districts'][] = $unDistrict;
                                }
                            }
                            
                        }
                    } 
                    $tabLocalisation[] = $unRegion;
                }
            }
            /*dd($tabLocalisation, $regionsInfrastructure, $districtsInfrastructure, $communesInfrastructure);

            $data["regions"] = $regionsInfrastructure;
            $data["districts"] = $districtsInfrastructure;
            $data["communes"] = $communesInfrastructure;
            $data["localites"] = $localitesInfrastructure;*/

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Localiation list_successfull",
                'data' => $tabLocalisation
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
     * @Route("/api/coordonne/region", name="localisation_region_delimitation", methods={"GET"})
     */
    public function getCoordonneeRegion(Request $request, LocalisationInfrastructureService $localisationInfrastructureService)
    {    
        $response = new Response();

        try {
            $data = [];
            
            $regionsInfrastructure = $localisationInfrastructureService->getCoordonneeRegion();
            
            //$districtsInfrastructure = $localisationInfrastructureService->getAllDistricts();

            //$communesInfrastructure = $localisationInfrastructureService->getAllCommunes();

            //$localitesInfrastructure = $localisationInfrastructureService->getAllLocalites();
            
            /*dd($tabLocalisation, $regionsInfrastructure, $districtsInfrastructure, $communesInfrastructure);

            $data["regions"] = $regionsInfrastructure;
            $data["districts"] = $districtsInfrastructure;
            $data["communes"] = $communesInfrastructure;
            $data["localites"] = $localitesInfrastructure;*/

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Localiation region delimitation list_successfull",
                'data' => $regionsInfrastructure
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
