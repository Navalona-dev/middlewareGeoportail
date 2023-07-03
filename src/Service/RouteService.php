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
        $result = $this->routeRepository->addInfrastructureRoute($data['categorie'], $data['localite'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['commune'], $data['pkDebut'], $data['rattache'], $data['gestionnaire'], $data['modeGestion'], $data['pkFin'], $data['largeurHausse'], $data['largeurAccotement'],$data['structure'], $data['region'], $data['district'], $data['longitude'], $data['latitude'], $data['photo1'], $data['photo2'], $data['photo3'], $data['precisionStructure'], $data['precisionModeGestion'], $data['photoName1'], $data['photoName2'], $data['photoName3']);
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
        return false;
    }

    public function getAllInfrastructuresRouteMinifie()
    {
        $routes = $this->routeRepository->getAllInfrastructuresRouteMinifie();
        if (count($routes) > 0) {
            return $routes;
        }
        return false;
    }
    
    public function updateInfrastructure($idInfra, $updateColonneInfra)
    {
        $result = $this->routeRepository->updateInfrastructure($idInfra, $updateColonneInfra);
        return $result;
    }

    public function addInfoInTableByInfrastructure($table, $colonnes, $values)
    {
        $result = $this->routeRepository->addInfoInTableByInfrastructure($table, $colonnes, $values);
        return $result;
    }

    public function updateInfrastructureTables($table , $idRow, $updateColonne)
    {
        $result = $this->routeRepository->updateInfrastructureTables($table, $idRow, $updateColonne);
        return $result;
    }

    public function getOneInfraInfo($infraId)
    {
        $routes = $this->routeRepository->getOneInfraInfo($infraId);
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    public function getAccotementRoute($idRoute = null)
    {
        if (null != $idRoute) {
            $accotements = $this->routeRepository->getAccotementRoute($idRoute);
            if (count($accotements) > 0) {
                return $accotements;
            }
            return false;
        }
        return false;
    }

    public function getFosseRoute($idRoute = null)
    {
        if (null != $idRoute) {
            $fosses = $this->routeRepository->getFosseRoute($idRoute);
            if (count($fosses) > 0) {
                return $fosses;
            }
            return false;
        }
        return false;
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
        $result = $this->routeRepository->addInfrastructureRouteSituation($idInfrastructure, $data['fonctionnel'], $data['causeNonFonctionel'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['etat']);
        
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
        $result = $this->routeRepository->addInfrastructureRouteStructure($idInfrastructure, $data['structureDeformation'], $data['structureFissure'], $data['structureFaiencage'], $data['structureNidPouleStructure'], $data['structureDeformation'], $data['structureTeteOndule'], $data['structureRavines'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureRouteAccotement($idInfrastructure = null, $data, $cote = null)
    {
        $result = $this->routeRepository->addInfrastructureRouteAccotement($idInfrastructure, $data['accotement'.$cote], $data['accotementDegrationSurface'.$cote], $data['accotementDentelleRive'.$cote], $data['accotementDenivellationChausseAccotement'.$cote], $data['accotementDestructionAffouillementAccotement'.$cote], $data['accotementNonRevetueDeformationProfil'.$cote], $data['accotementRevetue'.$cote], $data['accotementTypeRevetementAccotement'.$cote], $data['accotementPrecisionTypeAccotement'], $data['dateInformationAccotement'], $data['sourceInformationAccotement'], $data['modeAcquisitionInformationAccotement']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFosse($idInfrastructure = null, $data, $cote = null)
    {
        $result = $this->routeRepository->addInfrastructureRouteFosse($idInfrastructure, $data['coteFosse'.$cote], $data['fosseRevetuDegradationFosse'.$cote], $data['fosseRevetuSectionBouche'.$cote], $data['fosseNonRevetuFosseProfil'.$cote], $data['fosseNonRevetuEncombrement'.$cote], $data['fosseRevetu'.$cote], $data['dateInformationFosse'], $data['sourceInformationFosse'], $data['modeAcquisitionInformationFosse']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFoncier($idInfrastructure = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteFoncier($data['statut'], $data['numeroReference'], $data['nomProprietaire'], $idInfrastructure);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteTravaux($idInfrastructure = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteTravaux($idInfrastructure, $data['objetTravaux'], $data['consistanceTravaux'], $data['modeRealisationTravaux'], $data['maitreOuvrageTravaux'], $data['maitreOuvrageDelegueTravaux'], $data['maitreOuvrageTravaux'], $data['idControleSurveillanceTravaux'], $data['modePassationTravaux'], $data['porteAppelOffreTravaux'], $data['montantTravaux'], $data['numeroContratTravaux'], $data['dateContratTravaux'], $data['dateOrdreServiceTravaux'], $data['idTitulaireTravaux'], $data['resultatTravaux'], $data['motifRuptureContratTravaux'], $data['dateReceptionProvisoireTravaux'], $data['dateReceptionDefinitiveTravaux'], $data['ingenieurReceptionProvisoireTravaux'], $data['ingenieurReceptionDefinitiveTravaux'], $data['dateInformationTravaux'], $data['sourceInformationTravaux'], $data['modeAcquisitionInformationTravaux'], $data['precisionConsistanceTravaux'], $data['modeRealisationTravaux'], $data['bailleurTravaux']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFourniture($idInfrastructure = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteFourniture($data['objetContratFourniture'], $data['consistanceContratFourniture'], $data['materielsFourniture'], $data['entiteFourniture'], $data['modePassationFourniture'], $data['porteAppelOffreFourniture'], $data['montantFourniture'], $data['idTitulaireFourniture'], $data['numeroContratFourniture'], $data['dateContratFourniture'], $data['dateOrdreFourniture'], $data['resultatFourniture'], $data['raisonResiliationFourniture'], $data['ingenieurReceptionProvisoireFourniture'], $data['ingenieurReceptionDefinitiveFourniture'], $data['dateReceptionProvisoireFourniture'], $data['dateReceptionDefinitiveFourniture'], $idInfrastructure, $data['bailleurFourniture']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteEtudes($idInfrastructure = null, $data)
    {
        $result = $this->routeRepository->addInfrastructureRouteEtudes($idInfrastructure, $data['objetContratEtude'], $data['consistanceContratEtude'], $data['entiteEtude'], $data['idTitulaireEtude'], $data['montantContratEtude'], $data['numeroContratEtude'], $data['modePassationEtude'], $data['porteAppelOffreEtude'], $data['dateContratEtude'], $data['dateOrdreServiceEtude'], $data['resultatPrestationEtude'], $data['motifRuptureContratEtude'], $data['dateInformationEtude'], $data['sourceInformationEtude'], $data['modeAcquisitionInformationEtude'], $data['precisionConsistanceContratEtude'], $data['bailleurEtude']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function cleanTablesByIdInfrastructure($idInfrastructure = null, $type = null)
    {
        $this->routeRepository->cleanTablesByIdInfrastructure($idInfrastructure, $type);
    }

    public function getAllyRouteInfo()
    {
        $routeyInfo = $this->routeRepository->getAllyRouteInfo();
        if (count($routeyInfo) > 0) {
            return $routeyInfo;
        }
        return false;
    }

    public function update()
    {
        $this->entityManager->flush();
    }

}