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
use App\Repository\PortRepository;

class PortService
{
    private $tokenStorage;
    private $entityManager;
    private $session;
    private $portRepository;

    public function __construct(TokenStorageInterface  $TokenStorageInterface, EntityManager $entityManager, SessionInterface $session, PortRepository $portRepository)
    {
        $this->tokenStorage = $TokenStorageInterface;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->portRepository = $portRepository;
    }
    
    public function addInfrastructure($data)
    {
        $result = $this->portRepository->addInfrastructure($data['nom'], $data['categorie'], $data['indicatif'], $data['type'], null, $data['localite'], $data['communeTerrain'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['longitude'], $data['latitude'], $data['district'], $data['categoriePrecision'], $data['chargeMaximum'], $data['region'], $data['photo1'], $data['photo2'], $data['photo3'], $data['photoName1'], $data['photoName2'], $data['photoName3'], $data['moisOuverture'], $data['moisFermeture']);
        return $result;
    }

    public function getAllCategorieInfra()
    {
        $categories = $this->portRepository->getAllCategorieInfra();
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
        $routes = $this->portRepository->getAllInfrastructures();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    public function getAllInfrastructuresMinifie()
    {
        $routes = $this->portRepository->getAllInfrastructuresMinifie();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    public function updateInfrastructure($idInfra, $updateColonneInfra)
    {
        $result = $this->portRepository->updateInfrastructure($idInfra, $updateColonneInfra);
        return $result;
    }

    public function addInfoInTableByInfrastructure($table, $colonnes, $values)
    {
        $result = $this->portRepository->addInfoInTableByInfrastructure($table, $colonnes, $values);
        return $result;
    }

    public function updateInfrastructureTables($table , $idRow, $updateColonne)
    {
        $result = $this->portRepository->updateInfrastructureTables($table, $idRow, $updateColonne);
        return $result;
    }
    
    public function getOneInfraInfo($infraId)
    {
        $routes = $this->portRepository->getOneInfraInfo(intval($infraId));
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }

    public function getAllInfrastructuresBaseRoute()
    {
        $routes = $this->portRepository->getAllInfrastructuresBaseRoute();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }
    
    public function addInfrastructureRouteEtat($idInfrastructure, $data)
    {
        $result = $this->portRepository->addInfrastructureRouteEtat($idInfrastructure, $data['etat'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureSituation($idInfrastructure, $data)
    {
        $result = $this->portRepository->addInfrastructureSituation($idInfrastructure, $data['fonctionnel'], $data['motif'], $data['sourceInformationSituation'], $data['modeAcquisitionInformationSituation'], $data['etat'], $data['raisonPrecision']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureDonneCollecte($idInfrastructure = null, $data)
    {
        $result = $this->portRepository->addInfrastructureDonneCollecte($idInfrastructure, $data['existenceQuai'], $data['existenceFissureQuai'], $data['existenceFerraillageVisibleQuai'], $data['existenceRade'], $data['existenceFissureRade'], $data['existenceFerraillageVisibleRade'], $data['existenceDarse'], $data['existenceFissureDarse'], $data['existenceFerraillageVisibleDarse'], $data['existenceRampeEau'], $data['existenceFissureRampeEau'], $data['existenceFerraillageVisibleRampeEau'], $data['existenceElectricite'], $data['sourceElectricite'], $data['etatElectricite'], $data['existenceEau'], $data['sourceEau'], $data['etatEau'], $data['existenceWc'], $data['typeWc'], $data['etatWc'], $data['existenceDrainageEauPluviale'], $data['etatDrainageEauPluviale'], $data['existenceCloture'], $data['typeCloture'], $data['etatCloture'], $data['sourceInformationData'], $data['modeAcquisitionInformationData']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteStructure($idInfrastructure = null, $data)
    {
        $result = $this->portRepository->addInfrastructureRouteStructure($idInfrastructure, $data['structureDeformation'], $data['structureFissure'], $data['structureFaiencage'], $data['structureNidPouleStructure'], $data['structureDeformation'], $data['structureTeteOndule'], $data['structureRavines'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureRouteAccotement($idInfrastructure = null, $data)
    {
        $result = $this->portRepository->addInfrastructureRouteAccotement($idInfrastructure, $data['accotement'], $data['accotementDegrationSurface'], $data['accotementDentelleRive'], $data['accotementDenivellationChausseAccotement'], $data['accotementDestructionAffouillementAccotement'], $data['accotementNonRevetueDeformationProfil'], $data['accotementRevetue'], $data['accotementTypeRevetementAccotement'], $data['accotementPrecisionTypeAccotement'], $data['dateInformationAccotement'], $data['sourceInformationAccotement'], $data['modeAcquisitionInformationAccotement']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFosse($idInfrastructure = null, $data)
    {
        $result = $this->portRepository->addInfrastructureRouteFosse($idInfrastructure, $data['coteFosse'], $data['fosseRevetuDegradationFosse'], $data['fosseRevetuSectionBouche'], $data['fosseNonRevetuFosseProfil'], $data['fosseNonRevetuEncombrement'], $data['fosseRevetu'], $data['dateInformationFosse'], $data['sourceInformationFosse'], $data['modeAcquisitionInformationFosse']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureRouteFoncier($idInfrastructure = null, $data)
    {
        $result = $this->portRepository->addInfrastructureRouteFoncier($data['statut'], $data['numeroReference'], $data['nomProprietaire'], $idInfrastructure);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureTravaux($idInfrastructure = null, $data)
    {
        $result = $this->portRepository->addInfrastructureTravaux($idInfrastructure, $data['objetTravaux'], $data['consistanceTravaux'], $data['maitreOuvrageTravaux'], $data['maitreOuvrageDelegueTravaux'], $data['maitreOeuvreTravaux'], $data['idControleSurveillanceTravaux'], $data['modePassationTravaux'], $data['porteAppelOffreTravaux'], $data['montantTravaux'], $data['numeroContratTravaux'], $data['dateContratTravaux'], $data['dateOrdreServiceTravaux'], $data['idTitulaireTravaux'], $data['resultatTravaux'], $data['motifRuptureContratTravaux'], $data['dateReceptionProvisoireTravaux'], $data['dateReceptionDefinitiveTravaux'], $data['ingenieurReceptionProvisoireTravaux'], $data['ingenieurReceptionDefinitiveTravaux'], $data['dateInformationTravaux'], $data['sourceInformationTravaux'], $data['modeAcquisitionInformationTravaux'], $data['bailleurTravaux']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructurePhoto($idInfrastructure = null, $setUpdate)
    {
        $result = $this->portRepository->addInfrastructurePhoto($idInfrastructure, $setUpdate);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function getPhotoInfraInfo($infraId)
    {
        $routes = $this->portRepository->getPhotoInfraInfo(intval($infraId));
        if (count($routes) > 0) {
            return $routes;
        }
        return false;
    }

    public function addInfrastructureRouteFourniture($idInfrastructure = null, $data)
    {
        $result = $this->portRepository->addInfrastructureRouteFourniture($data['objetContratFourniture'], $data['consistanceContratFourniture'], $data['materielsFourniture'], $data['entiteFourniture'], $data['modePassationFourniture'], $data['porteAppelOffreFourniture'], $data['montantFourniture'], $data['idTitulaireFourniture'], $data['numeroContratFourniture'], $data['dateContratFourniture'], $data['dateOrdreFourniture'], $data['resultatFourniture'], $data['raisonResiliationFourniture'], $data['ingenieurReceptionProvisoireFourniture'], $data['ingenieurReceptionDefinitiveFourniture'], $data['dateReceptionProvisoireFourniture'], $data['dateReceptionDefinitiveFourniture'], $idInfrastructure, $data['bailleurFourniture']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureEtudes($idInfrastructure = null, $data)
    {
        $result = $this->portRepository->addInfrastructureEtudes($idInfrastructure, $data['objetContratEtude'], $data['consistanceContratEtude'], $data['entiteEtude'], $data['idTitulaireEtude'], $data['montantContratEtude'], $data['numeroContratEtude'], $data['modePassationEtude'], $data['porteAppelOffreEtude'], $data['dateContratEtude'], $data['dateOrdreServiceEtude'], $data['resultatPrestationEtude'], $data['motifRuptureContratEtude'], $data['dateInformationEtude'], $data['sourceInformationEtude'], $data['modeAcquisitionInformationEtude'], $data['bailleurEtude']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function cleanTablesByIdInfrastructure($idInfrastructure = null, $type = null)
    {
        $this->portRepository->cleanTablesByIdInfrastructure($idInfrastructure, $type);
    }

    public function getAllyRouteInfo()
    {
        $routeyInfo = $this->portRepository->getAllyRouteInfo();
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