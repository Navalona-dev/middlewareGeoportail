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
        $result = $this->routeRepository->addInfrastructureRoute($data['categorie'], $data['localite'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['commune'], $data['pkDebut'], $data['rattache'], $data['gestionnaire'], $data['modeGestion'], $data['pkFin'], $data['largeurHausse'], $data['largeurAccotement'], $data['region'], $data['district'], $data['longitude'], $data['latitude'], $data['photo1'], $data['photo2'], $data['photo3']);
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
        $result = $this->routeRepository->addInfrastructureRouteEtat($idInfrastructure, $data['etat'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureRouteSituation($idInfrastructure, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteSituation($idInfrastructure, $data['fonctionnel'], $data['causeNonFonctionel'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteSurface($idInfrastructure = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteSurface($idInfrastructure, $data['surfaceRevetement'], $data['surfaceNidPoule'], $data['surfaceArrachement'], $data['surfaceRessuage'], $data['surfaceFissureJoint'], $data['surfaceNonRevetuTraverse'], $data['surfaceBourbier'], $data['surfaceTeteChat'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteStructure($idInfrastructure = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteStructure($idInfrastructure, $data['structure']['deformation'], $data['structureFissure'], $data['structureFaiencage'], $data['structureNidPouleStructure'], $data['structureDeformation'], $data['structureTeteOndule'], $data['structureRavines'], $data['structureOrnierage'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
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
        $result = $this->routeRepository->addInfrastructureRouteAccotement($idCollecteDonne, $data['accotementTypeRevetementAccotement'], $data['accotementDegrationSurface'], $data['accotementDentelleRive'], $data['accotementDenivellationChausseAccotement'], $data['accotementDestructionAffouillementAccotement'], $data['accotementNonRevetueDeformationProfil'], $data['accotementHasAccotementRevetue']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFosse($idCollecteDonne = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteFosse($idCollecteDonne, $data['accotementTypeRevetementAccotement'], $data['fosseRevetuDegradationFosse'], $data['fosseRevetuSectionBouche'], $data['fosseNonRevetuFosseProfil'], $data['fosseNonRevetuEncombrement'], null);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function update()
    {
        $this->entityManager->flush();
    }

}