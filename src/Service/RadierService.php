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
use App\Repository\RadierRepository;

class RadierService
{
    private $tokenStorage;
    private $entityManager;
    private $session;
    private $radierRepository;

    public function __construct(TokenStorageInterface  $TokenStorageInterface, EntityManager $entityManager, SessionInterface $session, RadierRepository $radierRepository)
    {
        $this->tokenStorage = $TokenStorageInterface;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->radierRepository = $radierRepository;
    }
    
    public function addInfrastructure($data)
    {
        $result = $this->radierRepository->addInfrastructure($data['nom'], $data['categorie'], $data['indicatif'], $data['nomRouteRattache'], $data['pointKmImplantation'], $data['longueur'], $data['localite'], $data['communeTerrain'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['longitude'], $data['latitude'], $data['district'], $data['region'], $data['photo1'], $data['photo2'], $data['photo3'], $data['photoName1'], $data['photoName2'], $data['photoName3']);
        return $result;
    }

    /*public function addInfrastructureBaseRoute($multipleCoordonnée, $nom)
    {
        $result = $this->radierRepository->addInfrastructureBaseRoute($multipleCoordonnée, $nom);
        return $result;
    }*/

    public function getAllInfrastructures()
    {
        $routes = $this->radierRepository->getAllInfrastructures();
        if (count($routes) > 0) {
            return $routes;
        }
        return false;
    }

    public function getAllInfrastructuresMinifie()
    {
        $routes = $this->radierRepository->getAllInfrastructuresMinifie();
        if (count($routes) > 0) {
            return $routes;
        }
        return false;
    }

    public function updateInfrastructure($idInfra, $updateColonneInfra)
    {
        $result = $this->radierRepository->updateInfrastructure($idInfra, $updateColonneInfra);
        return $result;
    }

    public function addInfoInTableByInfrastructure($table, $colonnes, $values)
    {
        $result = $this->radierRepository->addInfoInTableByInfrastructure($table, $colonnes, $values);
        return $result;
    }

    public function updateInfrastructureTables($table , $idRow, $updateColonne)
    {
        $result = $this->radierRepository->updateInfrastructureTables($table, $idRow, $updateColonne);
        return $result;
    }
    
    public function getOneInfraInfo($infraId)
    {
        $routes = $this->radierRepository->getOneInfraInfo(intval($infraId));
        if (count($routes) > 0) {
            return $routes;
        }
        return false;
    }

    /*public function getAllInfrastructuresBaseRoute()
    {
        $routes = $this->radierRepository->getAllInfrastructuresBaseRoute();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }
    
    public function addInfrastructureRouteEtat($idInfrastructure, $data)
    {
        $result = $this->radierRepository->addInfrastructureRouteEtat($idInfrastructure, $data['etat'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }*/
    
    public function addInfrastructureSituation($idInfrastructure, $data)
    {
        $result = $this->radierRepository->addInfrastructureSituation($idInfrastructure, $data['fonctionnel'], $data['motif'], $data['sourceInformationSituation'], $data['modeAcquisitionInformationSituation'], $data['etat']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureDonneCollecte($idInfrastructure = null, $data)
    {
        $result = $this->radierRepository->addInfrastructureDonneCollecte($idInfrastructure, $data['hauteurDecalageJointureRadierTerrainNaturel'], $data['existenceFissures'], $data['existenceFerraillageVisible'], $data['denivellationStructureRadierCanalArrivee'], $data['denivellationChausseeRadier'], $data['sourceInformationData'], $data['modeAcquisitionInformationData']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function getInfoyRouteInfoMinifie($idYlisteRoute)
    {
        $routes = $this->radierRepository->getInfoyRouteInfoMinifie(intval($idYlisteRoute));
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    /*public function addInfrastructureRouteStructure($idInfrastructure = null, $data)
    {
        $result = $this->radierRepository->addInfrastructureRouteStructure($idInfrastructure, $data['structureDeformation'], $data['structureFissure'], $data['structureFaiencage'], $data['structureNidPouleStructure'], $data['structureDeformation'], $data['structureTeteOndule'], $data['structureRavines'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureRouteAccotement($idInfrastructure = null, $data)
    {
        $result = $this->radierRepository->addInfrastructureRouteAccotement($idInfrastructure, $data['accotement'], $data['accotementDegrationSurface'], $data['accotementDentelleRive'], $data['accotementDenivellationChausseAccotement'], $data['accotementDestructionAffouillementAccotement'], $data['accotementNonRevetueDeformationProfil'], $data['accotementRevetue'], $data['accotementTypeRevetementAccotement'], $data['accotementPrecisionTypeAccotement'], $data['dateInformationAccotement'], $data['sourceInformationAccotement'], $data['modeAcquisitionInformationAccotement']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFosse($idInfrastructure = null, $data)
    {
        $result = $this->radierRepository->addInfrastructureRouteFosse($idInfrastructure, $data['coteFosse'], $data['fosseRevetuDegradationFosse'], $data['fosseRevetuSectionBouche'], $data['fosseNonRevetuFosseProfil'], $data['fosseNonRevetuEncombrement'], $data['fosseRevetu'], $data['dateInformationFosse'], $data['sourceInformationFosse'], $data['modeAcquisitionInformationFosse']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFoncier($idInfrastructure = null, $data)
    {
        $result = $this->radierRepository->addInfrastructureRouteFoncier($data['statut'], $data['numeroReference'], $data['nomProprietaire'], $idInfrastructure);
        
        if ($result) {
            return $result;
        }

        return false;
    }*/

    public function addInfrastructureTravaux($idInfrastructure = null, $data)
    {
        $result = $this->radierRepository->addInfrastructureTravaux($idInfrastructure, $data['objetTravaux'], $data['consistanceTravaux'], $data['maitreOuvrageTravaux'], $data['maitreOuvrageTravaux'],$data['maitreOuvrageDelegueTravaux'], $data['maitreOeuvreTravaux'], $data['idControleSurveillanceTravaux'], $data['modePassationTravaux'], $data['porteAppelOffreTravaux'], $data['montantTravaux'], $data['numeroContratTravaux'], $data['dateContratTravaux'], $data['dateOrdreServiceTravaux'], $data['idTitulaireTravaux'], $data['resultatTravaux'], $data['motifRuptureContratTravaux'], $data['dateReceptionProvisoireTravaux'], $data['dateReceptionDefinitiveTravaux'], $data['ingenieurReceptionProvisoireTravaux'], $data['ingenieurReceptionDefinitiveTravaux'], $data['dateInformationTravaux'], $data['sourceInformationTravaux'], $data['modeAcquisitionInformationTravaux'], $data['bailleurTravaux'], $data['precisionConsistanceTravaux'], $data['precisionPassationTravaux']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function getAllyRouteInfoMinifie()
    {
        $routeyInfo = $this->radierRepository->getAllyRouteInfoMinifie();
        if (count($routeyInfo) > 0) {
            return $routeyInfo;
        }
        return false;
    }

    public function addInfrastructurePhoto($idInfrastructure = null, $setUpdate)
    {
        $result = $this->radierRepository->addInfrastructurePhoto($idInfrastructure, $setUpdate);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function getPhotoInfraInfo($infraId)
    {
        $routes = $this->radierRepository->getPhotoInfraInfo(intval($infraId));
        if (count($routes) > 0) {
            return $routes;
        }
        return false;
    }

    public function addInfrastructureFourniture($idInfrastructure = null, $data)
    {
        $result = $this->radierRepository->addInfrastructureFourniture($data['objetContratFourniture'], $data['consistanceContratFourniture'], $data['materielsFourniture'], $data['entiteFourniture'], $data['modePassationFourniture'], $data['porteAppelOffreFourniture'], $data['montantFourniture'], $data['idTitulaireFourniture'], $data['numeroContratFourniture'], $data['dateContratFourniture'], $data['dateOrdreFourniture'], $data['resultatFourniture'], $data['raisonResiliationFourniture'], $data['ingenieurReceptionProvisoireFourniture'], $data['ingenieurReceptionDefinitiveFourniture'], $data['dateReceptionProvisoireFourniture'], $data['dateReceptionDefinitiveFourniture'], $idInfrastructure, $data['bailleurFourniture'], $data['precisionPassationFourniture']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureEtudes($idInfrastructure = null, $data)
    {
        $result = $this->radierRepository->addInfrastructureEtudes($idInfrastructure, $data['objetContratEtude'], $data['consistanceContratEtude'], $data['entiteEtude'], $data['idTitulaireEtude'], $data['montantContratEtude'], $data['numeroContratEtude'], $data['modePassationEtude'], $data['porteAppelOffreEtude'], $data['dateContratEtude'], $data['dateOrdreServiceEtude'], $data['resultatPrestationEtude'], $data['motifRuptureContratEtude'], $data['dateInformationEtude'], $data['sourceInformationEtude'], $data['modeAcquisitionInformationEtude'], $data['precisionConsistanceEtude'], $data['bailleurEtude']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function cleanTablesByIdInfrastructure($idInfrastructure = null, $type = null)
    {
        $this->radierRepository->cleanTablesByIdInfrastructure($idInfrastructure, $type);
    }

    public function getAllyRouteInfo()
    {
        $routeyInfo = $this->radierRepository->getAllyRouteInfo();
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