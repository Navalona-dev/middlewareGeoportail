<?php
namespace App\Service;

use App\Service\AuthorizationManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Exception\PropertyVideException;
use App\Exception\ActionInvalideException;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use App\Repository;
use App\Repository\RouteRepository;

class RouteService
{
    private $tokenStorage;
    private $entityManager;
    private $session;
    private $routeRepository;

    public function __construct(TokenStorageInterface  $TokenStorageInterface, EntityManager $entityManager, SessionInterface $session, RouteRepository $routeRepository)
    {
        $this->tokenStorage = $TokenStorageInterface;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->routeRepository = $routeRepository;
    }
    
    public function addInfrastructureRoute($data)
    {
        $result = $this->routeRepository->addInfrastructureRoute($data['categorie'], $data['localite'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['communeTerrain'], $data['pk']['debut'], $data['section'], $data['numeroRoute'], $data['gestionnaire'], $data['modeGestion'], null,  $data['pk']['fin'], null, $data['largeur']['hausse'], $data['largeur']['accotement'], $data['structure'], $data['region'], $data['district'], $data['gps'], $data['longitude'], $data['latitude']);
        return $result;
    }

    public function addInfrastructureBaseRoute($multipleCoordonnée, $nom)
    {
        $result = $this->routeRepository->addInfrastructureBaseRoute($multipleCoordonnée, $nom);
        return $result;
    }

    public function getAllInfrastructuresRoute()
    {
        $routes = $this->routeRepository->getAllInfrastructuresRoute();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    public function getAllInfrastructuresBaseRoute()
    {
        $routes = $this->routeRepository->getAllInfrastructuresBaseRoute();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }
    
    public function addInfrastructureRouteEtat($idInfrastructure, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteEtat($idInfrastructure, $data['infoSupplementaire']['etat'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureRouteSituation($idInfrastructure, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteSituation($idInfrastructure, $data['infoSupplementaire']['fonctionnel'], $data['infoSupplementaire']['causeNonFonctinel'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteSurface($idInfrastructure = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteSurface($idInfrastructure, $data['infoSupplementaire']['surface']['revetement'], $data['infoSupplementaire']['surface']['nidPoule'], $data['infoSupplementaire']['surface']['arrachement'], $data['infoSupplementaire']['surface']['ressuage'], $data['infoSupplementaire']['surface']['fissureJoint'], $data['infoSupplementaire']['surface']['nonRevetuTraverse'], $data['infoSupplementaire']['surface']['bourbier'], $data['infoSupplementaire']['surface']['teteChat'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteStructure($idInfrastructure = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteStructure($idInfrastructure, $data['infoSupplementaire']['structure']['deformation'], $data['infoSupplementaire']['structure']['fissure'], $data['infoSupplementaire']['structure']['faiencage'], $data['infoSupplementaire']['structure']['nidPouleStructure'], $data['infoSupplementaire']['structure']['deformation'], $data['infoSupplementaire']['structure']['teteOndule'], $data['infoSupplementaire']['structure']['ravines'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteCollecte($idInfrastructure = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteCollecte($idInfrastructure, $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureRouteAccotement($idCollecteDonne = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteAccotement($idCollecteDonne, $data['infoSupplementaire']['accotement']['typeRevetementAccotement'], $data['infoSupplementaire']['accotement']['degrationSurface'], $data['infoSupplementaire']['accotement']['dentelleRive'], $data['infoSupplementaire']['accotement']['denivellationChausseAccotement'], $data['infoSupplementaire']['accotement']['destructionAffouillementAccotement'], $data['infoSupplementaire']['accotement']['nonRevetueDeformationProfil'], $data['infoSupplementaire']['accotement']['hasAccotementRevetue']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFosse($idCollecteDonne = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteFosse($idCollecteDonne, $data['infoSupplementaire']['accotement']['typeRevetementAccotement'], $data['infoSupplementaire']['fosse']['revetuDegradationFosse'], $data['infoSupplementaire']['fosse']['revetuSectionBouche'], $data['infoSupplementaire']['fosse']['nonRevetuFosseProfil'], $data['infoSupplementaire']['fosse']['nonRevetuEncombrement'], null);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function update()
    {
        $this->entityManager->flush();
    }

    public function remove($permission)
    {
        $this->entityManager->remove($permission);
        $this->update();
    }

}