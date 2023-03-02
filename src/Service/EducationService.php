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