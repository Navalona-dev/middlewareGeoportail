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
use App\Repository\EducationRepository;

class EducationService
{
    private $tokenStorage;
    private $entityManager;
    private $session;
    private $educationRepository;

    public function __construct(TokenStorageInterface  $TokenStorageInterface, EntityManager $entityManager, SessionInterface $session, EducationRepository $educationRepository)
    {
        $this->tokenStorage = $TokenStorageInterface;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->educationRepository = $educationRepository;
    }
    
    public function addInfrastructure($data)
    {
        $result = $this->educationRepository->addInfrastructure($data['nom'], $data['categorie'], $data['indicatif'], $data['volumeReservoir'], null, $data['localite'], $data['communeTerrain'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['longitude'], $data['latitude'], $data['district'], $data['categoriePrecision'], $data['chargeMaximum'], $data['region'], $data['photo1'], $data['photo2'], $data['photo3'], $data['photoName1'], $data['photoName2'], $data['photoName3'], $data['moisOuverture'], $data['moisFermeture']);
        return $result;
    }

    public function getAllCategorieInfra()
    {
        $categories = $this->educationRepository->getAllCategorieInfra();
        if (count($categories) > 0) {
            $tabCategorie = [];
            foreach ($categories as $key => $categorie) {
                if (!in_array($categorie['categorie'], $tabCategorie)) {
                    array_push($tabCategorie, $categorie['categorie']);
                }
            }
            return $tabCategorie;
        }
        return false;
    }

    public function getAllInfrastructures()
    {
        $routes = $this->educationRepository->getAllInfrastructures();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    public function getAllInfrastructuresMinifie()
    {
        $routes = $this->educationRepository->getAllInfrastructuresMinifie();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    public function updateInfrastructure($idInfra, $updateColonneInfra)
    {
        $result = $this->educationRepository->updateInfrastructure($idInfra, $updateColonneInfra);
        return $result;
    }

    public function addInfoInTableByInfrastructure($table, $colonnes, $values)
    {
        $result = $this->educationRepository->addInfoInTableByInfrastructure($table, $colonnes, $values);
        return $result;
    }

    public function updateInfrastructureTables($table , $idRow, $updateColonne)
    {
        $result = $this->educationRepository->updateInfrastructureTables($table, $idRow, $updateColonne);
        return $result;
    }
    
    public function getOneInfraInfo($infraId)
    {
        $routes = $this->educationRepository->getOneInfraInfo(intval($infraId));
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    public function getAllInfrastructuresBaseRoute()
    {
        $routes = $this->educationRepository->getAllInfrastructuresBaseRoute();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }
    
    public function addInfrastructureRouteEtat($idInfrastructure, $data)
    {
        $result = $this->educationRepository->addInfrastructureRouteEtat($idInfrastructure, $data['etat'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureSituation($idInfrastructure, $data)
    {
        $result = $this->educationRepository->addInfrastructureSituation($idInfrastructure, $data['fonctionnel'], $data['motif'], $data['sourceInformationSituation'], $data['modeAcquisitionInformationSituation'], $data['etat'], $data['raisonPrecision']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureDonneCollecte($idInfrastructure = null, $data = [])
    {
        $result = $this->educationRepository->addInfrastructureDonneCollecte($idInfrastructure, $data['existenceElectricite'], $data['sourceElectricite'], $data['etatElectricite'], $data['existenceEau'], $data['sourceEau'], $data['etatEau'], $data['existenceWc'], $data['typeWc'], $data['etatWc'], $data['existenceDrainageEauPluviale'], $data['etatDrainageEauPluviale'], $data['existenceCloture'], $data['typeCloture'], $data['etatCloture'], $data['existenceTerrainFoot'], $data['etatTerrainFoot'], $data['existenceTerrainMixte'], $data['etatTerrainMixte'], $data['existenceCantine'], $data['nombreEnseignant'], $data['nombreEleve'], $data['sourceInformationData'], $data['modeAcquisitionInformationData']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteStructure($idInfrastructure = null, $data = [])
    {
        $result = $this->educationRepository->addInfrastructureRouteStructure($idInfrastructure, $data['structureDeformation'], $data['structureFissure'], $data['structureFaiencage'], $data['structureNidPouleStructure'], $data['structureDeformation'], $data['structureTeteOndule'], $data['structureRavines'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureRouteAccotement($idInfrastructure = null, $data = [])
    {
        $result = $this->educationRepository->addInfrastructureRouteAccotement($idInfrastructure, $data['accotement'], $data['accotementDegrationSurface'], $data['accotementDentelleRive'], $data['accotementDenivellationChausseAccotement'], $data['accotementDestructionAffouillementAccotement'], $data['accotementNonRevetueDeformationProfil'], $data['accotementRevetue'], $data['accotementTypeRevetementAccotement'], $data['accotementPrecisionTypeAccotement'], $data['dateInformationAccotement'], $data['sourceInformationAccotement'], $data['modeAcquisitionInformationAccotement']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFosse($idInfrastructure = null, $data = [])
    {
        $result = $this->educationRepository->addInfrastructureRouteFosse($idInfrastructure, $data['coteFosse'], $data['fosseRevetuDegradationFosse'], $data['fosseRevetuSectionBouche'], $data['fosseNonRevetuFosseProfil'], $data['fosseNonRevetuEncombrement'], $data['fosseRevetu'], $data['dateInformationFosse'], $data['sourceInformationFosse'], $data['modeAcquisitionInformationFosse']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFoncier($idInfrastructure = null, $data = [])
    {
        $result = $this->educationRepository->addInfrastructureRouteFoncier($data['statut'], $data['numeroReference'], $data['nomProprietaire'], $idInfrastructure);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureTravaux($idInfrastructure = null, $data = [])
    {
        $result = $this->educationRepository->addInfrastructureTravaux($idInfrastructure, $data['objetTravaux'], $data['consistanceTravaux'], $data['maitreOuvrageTravaux'], $data['maitreOuvrageDelegueTravaux'], $data['maitreOeuvreTravaux'], $data['idControleSurveillanceTravaux'], $data['modePassationTravaux'], $data['porteAppelOffreTravaux'], $data['montantTravaux'], $data['numeroContratTravaux'], $data['dateContratTravaux'], $data['dateOrdreServiceTravaux'], $data['idTitulaireTravaux'], $data['resultatTravaux'], $data['motifRuptureContratTravaux'], $data['dateReceptionProvisoireTravaux'], $data['dateReceptionDefinitiveTravaux'], $data['ingenieurReceptionProvisoireTravaux'], $data['ingenieurReceptionDefinitiveTravaux'], $data['dateInformationTravaux'], $data['sourceInformationTravaux'], $data['modeAcquisitionInformationTravaux'], $data['bailleurTravaux']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructurePhoto($idInfrastructure = null, $setUpdate = null)
    {
        $result = $this->educationRepository->addInfrastructurePhoto($idInfrastructure, $setUpdate);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function getPhotoInfraInfo($infraId)
    {
        $routes = $this->educationRepository->getPhotoInfraInfo(intval($infraId));
        if (count($routes) > 0) {
            return $routes;
        }
        return false;
    }

    public function addInfrastructureRouteFourniture($idInfrastructure = null, $data = [])
    {
        $result = $this->educationRepository->addInfrastructureRouteFourniture($data['objetContratFourniture'], $data['consistanceContratFourniture'], $data['materielsFourniture'], $data['entiteFourniture'], $data['modePassationFourniture'], $data['porteAppelOffreFourniture'], $data['montantFourniture'], $data['idTitulaireFourniture'], $data['numeroContratFourniture'], $data['dateContratFourniture'], $data['dateOrdreFourniture'], $data['resultatFourniture'], $data['raisonResiliationFourniture'], $data['ingenieurReceptionProvisoireFourniture'], $data['ingenieurReceptionDefinitiveFourniture'], $data['dateReceptionProvisoireFourniture'], $data['dateReceptionDefinitiveFourniture'], $idInfrastructure, $data['bailleurFourniture']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureEtudes($idInfrastructure = null, $data = [])
    {
        $result = $this->educationRepository->addInfrastructureEtudes($idInfrastructure, $data['objetContratEtude'], $data['consistanceContratEtude'], $data['entiteEtude'], $data['idTitulaireEtude'], $data['montantContratEtude'], $data['numeroContratEtude'], $data['modePassationEtude'], $data['porteAppelOffreEtude'], $data['dateContratEtude'], $data['dateOrdreServiceEtude'], $data['resultatPrestationEtude'], $data['motifRuptureContratEtude'], $data['dateInformationEtude'], $data['sourceInformationEtude'], $data['modeAcquisitionInformationEtude'], $data['bailleurEtude']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function cleanTablesByIdInfrastructure($idInfrastructure = null, $type = null)
    {
        $this->educationRepository->cleanTablesByIdInfrastructure($idInfrastructure, $type);
    }

    public function getAllyRouteInfo()
    {
        $routeyInfo = $this->educationRepository->getAllyRouteInfo();
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