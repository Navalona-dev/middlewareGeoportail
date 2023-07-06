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
use App\Repository\TrajetrouteRepository;

class TrajetrouteService
{
    private $tokenStorage;
    private $entityManager;
    private $session;
    private $trajetrouteRepository;

    public function __construct(TokenStorageInterface  $TokenStorageInterface, EntityManager $entityManager, SessionInterface $session, TrajetrouteRepository $trajetrouteRepository)
    {
        $this->tokenStorage = $TokenStorageInterface;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->trajetrouteRepository = $trajetrouteRepository;
    }
    
    public function addInfrastructure($data)
    {
        $result = $this->trajetrouteRepository->addInfrastructure($data['nom'], $data['nomRouteRattache'], $data['localiteDepart'], $data['localiteArrive'], $data['pkDepart'], $data['pkArrive'], $data['categorie'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['coordonnees'], $data['photo1'], $data['photo2'], $data['photo3'], $data['photoName1'], $data['photoName2'], $data['photoName3']);
        return $result;
    }

    public function addInfrastructureBaseRoute($multipleCoordonnée, $nom)
    {
        $result = $this->trajetrouteRepository->addInfrastructureBaseRoute($multipleCoordonnée, $nom);
        return $result;
    }

    public function getAllInfrastructures()
    {
        $routes = $this->trajetrouteRepository->getAllInfrastructures();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    public function getAllInfrastructuresMinifie()
    {
        $routes = $this->trajetrouteRepository->getAllInfrastructuresMinifie();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    public function updateInfrastructure($idInfra, $updateColonneInfra)
    {
        $result = $this->trajetrouteRepository->updateInfrastructure($idInfra, $updateColonneInfra);
        return $result;
    }

    public function addInfoInTableByInfrastructure($table, $colonnes, $values)
    {
        $result = $this->trajetrouteRepository->addInfoInTableByInfrastructure($table, $colonnes, $values);
        return $result;
    }

    public function updateInfrastructureTables($table , $idRow, $updateColonne)
    {
        $result = $this->trajetrouteRepository->updateInfrastructureTables($table, $idRow, $updateColonne);
        return $result;
    }

    public function getOneInfraInfo($infraId)
    {
        $routes = $this->trajetrouteRepository->getOneInfraInfo(intval($infraId));
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    public function getAllInfrastructuresBaseRoute()
    {
        $routes = $this->trajetrouteRepository->getAllInfrastructuresBaseRoute();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }
    
    public function addInfrastructureRouteEtat($idInfrastructure, $data)
    {
        $result = $this->trajetrouteRepository->addInfrastructureRouteEtat($idInfrastructure, $data['etat'], $data['sourceInformationEtat'], $data['modeAcquisitionInformationEtat']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureSituation($idInfrastructure, $data)
    {
        $result = $this->trajetrouteRepository->addInfrastructureSituation($idInfrastructure, $data['fonctionnel'], $data['motif'], $data['sourceInformationSituation'], $data['modeAcquisitionInformationSituation'], $data['etat'], $data['raisonPrecision']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureDonneCollecte($idInfrastructure = null, $data)
    {
        $result = $this->trajetrouteRepository->addInfrastructureDonneCollecte($idInfrastructure, $data['praticableAnnee'], $data['moisOuverture'], $data['moisFermeture'], $data['dureeTrajetSaisonSeche'], $data['sourceInformationData'], $data['modeAcquisitionInformationData'], $data['revetementData'], $data['dateInformationData']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteStructure($idInfrastructure = null, $data)
    {
        $result = $this->trajetrouteRepository->addInfrastructureRouteStructure($idInfrastructure, $data['structureDeformation'], $data['structureFissure'], $data['structureFaiencage'], $data['structureNidPouleStructure'], $data['structureDeformation'], $data['structureTeteOndule'], $data['structureRavines'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructurePhoto($idInfrastructure = null, $data)
    {
        $result = $this->trajetrouteRepository->addInfrastructurePhoto($idInfrastructure, $data['photo1'], $data['photo2'], $data['photo3'], $data['photoName1'], $data['photoName2'], $data['photoName3']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteAccotement($idInfrastructure = null, $data)
    {
        $result = $this->trajetrouteRepository->addInfrastructureRouteAccotement($idInfrastructure, $data['accotement'], $data['accotementDegrationSurface'], $data['accotementDentelleRive'], $data['accotementDenivellationChausseAccotement'], $data['accotementDestructionAffouillementAccotement'], $data['accotementNonRevetueDeformationProfil'], $data['accotementRevetue'], $data['accotementTypeRevetementAccotement'], $data['accotementPrecisionTypeAccotement'], $data['dateInformationAccotement'], $data['sourceInformationAccotement'], $data['modeAcquisitionInformationAccotement']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFosse($idInfrastructure = null, $data)
    {
        $result = $this->trajetrouteRepository->addInfrastructureRouteFosse($idInfrastructure, $data['coteFosse'], $data['fosseRevetuDegradationFosse'], $data['fosseRevetuSectionBouche'], $data['fosseNonRevetuFosseProfil'], $data['fosseNonRevetuEncombrement'], $data['fosseRevetu'], $data['dateInformationFosse'], $data['sourceInformationFosse'], $data['modeAcquisitionInformationFosse']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFoncier($idInfrastructure = null, $data)
    {
        $result = $this->trajetrouteRepository->addInfrastructureRouteFoncier($idInfrastructure, $data['statut'], $data['numeroReference'], $data['nomProprietaire'], $data['sourceInformationFoncier'], $data['modeAcquisitionInformationFoncier']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureTravaux($idInfrastructure = null, $data)
    {
        $result = $this->trajetrouteRepository->addInfrastructureTravaux($idInfrastructure, $data['consistanceTravaux'], $data['objetTravaux'], $data['modeRealisationTravaux'], $data['maitreOuvrageTravaux'], $data['maitreOuvrageDelegueTravaux'], $data['maitreOeuvreTravaux'], $data['idControleSurveillanceTravaux'], $data['modePassationTravaux'], $data['porteAppelOffreTravaux'], $data['montantTravaux'], $data['numeroContratTravaux'], $data['dateContratTravaux'], $data['dateOrdreServiceTravaux'], $data['idTitulaireTravaux'], $data['resultatTravaux'], $data['motifRuptureContratTravaux'], $data['dateReceptionProvisoireTravaux'], $data['dateReceptionDefinitiveTravaux'], $data['ingenieurReceptionProvisoireTravaux'], $data['ingenieurReceptionDefinitiveTravaux'], $data['dateInformationTravaux'], $data['sourceInformationTravaux'], $data['modeAcquisitionInformationTravaux'], $data['bailleurTravaux'], $data['precisionConsitanceTravaux'], $data['precisionPassationTravaux']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFourniture($idInfrastructure = null, $data)
    {
        $result = $this->trajetrouteRepository->addInfrastructureRouteFourniture($data['objetContratFourniture'], $data['materielsFouriniture'], $data['entiteFourniture'], $data['modePassationFourniture'], $data['porteAppelOffreFourniture'], $data['montantFourniture'], $data['idTitulaireFourniture'], $data['numeroContratFourniture'], $data['dateContratFourniture'], $data['dateOrdreFourniture'], $data['resultatFourniture'], $data['raisonResiliationFourniture'], $idInfrastructure, $data['bailleurFourniture'], $data['consistanceContratFourniture'], $data['precisionPassationFourniture']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureEtudes($idInfrastructure = null, $data)
    {
        $result = $this->trajetrouteRepository->addInfrastructureEtudes($idInfrastructure, $data['objetContratEtude'], $data['consistanceContratEtude'], $data['entiteEtude'], $data['idTitulaireEtude'], $data['montantContratEtude'], $data['numeroContratEtude'], $data['modePassationEtude'], $data['porteAppelOffreEtude'], $data['dateContratEtude'], $data['dateOrdreServiceEtude'], $data['resultatPrestationEtude'], $data['motifRuptureContratEtude'], $data['bailleurEtude'], $data['precisionConsitanceEtude'], $data['precisionPassationEtude']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function cleanTablesByIdInfrastructure($idInfrastructure = null, $type = null)
    {
        $this->trajetrouteRepository->cleanTablesByIdInfrastructure($idInfrastructure, $type);
    }

    public function getAllyRouteInfo()
    {
        $routeyInfo = $this->trajetrouteRepository->getAllyRouteInfo();
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