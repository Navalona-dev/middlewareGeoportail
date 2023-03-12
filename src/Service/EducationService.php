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
    
    public function addInfrastructureEducation($data)
    {
        $result = $this->educationRepository->addInfrastructureEducation($data['nom'], $data['indicatif'], $data['categorie'], $data['localite'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['communeTerrain'], $data['numeroSequence'], (int) $data['codeProduit'], (int) $data['codeCommune'], (float) $data['latitude'],(float) $data['longitude'], $data['sousCategorie'], $data['district'], $data['photo1'], $data['photo2'], $data['photo3'], $data['photoName1'], $data['photoName2'], $data['photoName3']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function getAllInfrastructuresEducation($data)
    {
        $educations = $this->educationRepository->getAllInfrastructuresEducation();
        if (count($educations) > 0) {
            return $educations;
        }
        return 0;
    }
    
    public function addInfrastructureEducationEtat($idInfrastructure, $data)
    {
        $result = $this->educationRepository->addInfrastructureEducationEtat($idInfrastructure, $data['infoSupplementaire']['etat'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureEducationSituation($idInfrastructure, $data)
    {
        $result = $this->educationRepository->addInfrastructureEducationSituation($idInfrastructure, $data['infoSupplementaire']['fonctionnel'], $data['infoSupplementaire']['causeNonFonctinel'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureEducationDonneAnnexe($idInfrastructure, $data)
    {
        $result = $this->educationRepository->addInfrastructureEducationDonneAnnexe($idInfrastructure, $data['infoSupplementaire']['existenceCantine'], $data['infoSupplementaire']['nombreEnseignant'], $data['infoSupplementaire']['nombreEleve'], $data['sourceInformation'], $data['modeAcquisitionInformation']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function addInfrastructureEducationFoncier($idInfrastructure = null, $data)
    {
        $result = $this->educationRepository->addInfrastructureEducationFoncier($idInfrastructure, $data['statut'], $data['nomProprietaire'], $data['numeroReference'], $data['dateInformationFoncier'], $data['sourceInformationFoncier'], $data['modeAcquisitionInformationFoncier']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureEducationTravaux($idInfrastructure = null, $data)
    {
        $result = $this->educationRepository->addInfrastructureEducationTravaux($idInfrastructure, $data['objetTravaux'], $data['consistanceTravaux'], $data['maitreOuvrageTravaux'], $data['maitreOuvrageDelegueTravaux'], $data['maitreOeuvreTravaux'], $data['idControleSurveillanceTravaux'], $data['modePassationTravaux'], $data['porteAppelOffreTravaux'], $data['montantTravaux'], $data['numeroContratTravaux'], $data['dateContratTravaux'], $data['dateOrdreServiceTravaux'], $data['idTitulaireTravaux'], $data['resultatTravaux'], $data['motifRuptureContratTravaux'], $data['dateReceptionProvisoireTravaux'], $data['dateReceptionDefinitiveTravaux'], $data['ingenieurReceptionProvisoireTravaux'], $data['ingenieurReceptionDefinitiveTravaux'], $data['dateInformationTravaux'], $data['sourceInformationTravaux'], $data['modeAcquisitionInformationTravaux']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureEducationFourniture($idInfrastructure = null, $data)
    {
        $result = $this->educationRepository->addInfrastructureEducationFourniture($idInfrastructure, $data['objetContratFourniture'], $data['consistanceContratFourniture'], $data['materielsFourniture'], $data['entiteFourniture'], $data['modePassationFourniture'], $data['porteAppelOffreFourniture'], $data['montantFourniture'], $data['idTitulaireFourniture'], $data['numeroContratFourniture'], $data['dateContratFourniture'], $data['dateOrdreFourniture'], $data['resultatFourniture'], $data['raisonResiliationFourniture'], $data['ingenieurReceptionProvisoireFourniture'], $data['ingenieurReceptionDefinitiveFourniture'], $data['dateReceptionProvisoireFourniture'], $data['dateReceptionDefinitiveFourniture'], $data['dateInformationFourniture'], $data['sourceInformationFourniture'], $data['modeAcquisitionInformationFourniture']);
        
        if ($result) {
            return $result;
        }

        return false;
    }

    public function addInfrastructureEducationEtudes($idInfrastructure = null, $data)
    {
        dd($data);
        $result = $this->educationRepository->addInfrastructureEducationEtudes($idInfrastructure, $data['objetContratEtude'], $data['consistanceContratEtude'], $data['entiteEtude'], $data['idTitulaireEtude'], $data['montantContratEtude'], $data['numeroContratEtude'], $data['modePassationEtude'], $data['porteAppelOffreEtude'], $data['dateContratEtude'], $data['dateOrdreServiceEtude'], $data['resultatPrestationEtude'], $data['motifRuptureContratEtude'], $data['dateInformationEtude'], $data['sourceInformationEtude'], $data['modeAcquisitionInformationEtude']);
        
        if ($result) {
            return $result;
        }

        return false;
    }
    
    public function update()
    {
        $this->entityManager->flush();
    }

    public function remove($education)
    {
        $this->entityManager->remove($education);
        $this->update();
    }

}