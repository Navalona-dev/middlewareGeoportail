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
use App\Repository\InfrastructureRepository;

class InfrastructureService
{
    private $tokenStorage;
    private $entityManager;
    private $session;
    private $infrastructureRepository;

    public function __construct(TokenStorageInterface  $TokenStorageInterface, EntityManager $entityManager, SessionInterface $session, InfrastructureRepository $infrastructureRepository)
    {
        $this->tokenStorage = $TokenStorageInterface;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->infrastructureRepository = $infrastructureRepository;
    }

    /*public function add($instance)
    {
        $permission = Permission::newPermission($instance->getCategoryofpermission(), $instance->getTitle());

        $permission->setDescription($instance->getDescription());

        $this->entityManager->persist($permission);
        $this->update();
        unset($instance);
        return $permission;
    }*/

    public function getAllDomainesInfrastructure()
    {
        $domainesInfrastructures = $this->infrastructureRepository->getAllDomainesInfrastructure();
        if (count($domainesInfrastructures) > 0) {
            return $domainesInfrastructures;
        }
        return false;
    }

    public function getAllNiveauInfrastructureByDomaine($domaine = null)
    {
        $niveauxInfrastructures = $this->infrastructureRepository->getAllNiveauInfrastructureByDomaine($domaine);
        if (count($niveauxInfrastructures) > 0) {
            return $niveauxInfrastructures;
        }
        return false;
    }

    public function getAllNiveauInfrastructureByDomaineNiveau3($domaine = null)
    {
        $niveauxInfrastructures = $this->infrastructureRepository->getAllNiveauInfrastructureByDomaineNiveau3($domaine);
        if (count($niveauxInfrastructures) > 0) {
            return $niveauxInfrastructures;
        }
        return false;
    }

    public function getAllSourceInfo()
    {
        $sourceInformations = $this->infrastructureRepository->getAllSourceInfo();
        if (count($sourceInformations) > 0) {
            return $sourceInformations;
        }
        return false;
    }

    public function getAllIndicatifNiveau3()
    {
        $indicatifNiveau3 = $this->infrastructureRepository->getAllIndicatifNiveau3();
        if (count($indicatifNiveau3) > 0) {
            return $indicatifNiveau3;
        }
        return false;
    }
    
    public function getAllIndicatifNiveau2()
    {
        $indicatifNiveau3 = $this->infrastructureRepository->getAllIndicatifNiveau2();
        if (count($indicatifNiveau3) > 0) {
            return $indicatifNiveau3;
        }
        return false;
    }

    public function getAllPrestataireInfo()
    {
        $prestatairesInfo = $this->infrastructureRepository->getAllPrestataireInfo();
        if (count($prestatairesInfo) > 0) {
            return $prestatairesInfo;
        }
        return false;
    }

    public function getAllCategorieInfo()
    {
        $categorieInfo = $this->infrastructureRepository->getAllCategorieInfo();
        if (count($categorieInfo) > 0) {
            return $categorieInfo;
        }
        return false;
    }

    public function getOuiNonInfo()
    {
        $ouiNonInfo = $this->infrastructureRepository->getOuiNonInfo();
        if (count($ouiNonInfo) > 0) {
            return $ouiNonInfo;
        }
        return false;
    }

    public function getMotifNonFonctionnelInfo()
    {
        $motifNonFonctionnelInfo = $this->infrastructureRepository->getMotifNonFonctionnelInfo();
        if (count($motifNonFonctionnelInfo) > 0) {
            return $motifNonFonctionnelInfo;
        }
        return false;
    }

    public function getModeAcquisitionInfo()
    {
        $modeAcquisitionInfo = $this->infrastructureRepository->getModeAcquisitionInfo();
        if (count($modeAcquisitionInfo) > 0) {
            return $modeAcquisitionInfo;
        }
        return false;
    }

    public function getModePassationMarcheInfo()
    {
        $modePassationMarcheInfo = $this->infrastructureRepository->getModePassationMarcheInfo();
        if (count($modePassationMarcheInfo) > 0) {
            return $modePassationMarcheInfo;
        }
        return false;
    }

    public function getAllIngenieursInfo()
    {
        $ingenieursInfo = $this->infrastructureRepository->getAllIngenieursInfo();
        if (count($ingenieursInfo) > 0) {
            return $ingenieursInfo;
        }
        return false;
    }

    public function getAllMaitreOuvrageInfo()
    {
        $maitreOuvrageInfo = $this->infrastructureRepository->getAllMaitreOuvrageInfo();
        if (count($maitreOuvrageInfo) > 0) {
            return $maitreOuvrageInfo;
        }
        return false;
    }
    
    public function getMotifRuptureContratInfo($type = null)
    {
        $motifRuptureContratInfo = $this->infrastructureRepository->getMotifRuptureContratInfo($type);
        if (count($motifRuptureContratInfo) > 0) {
            return $motifRuptureContratInfo;
        }
        return false;
    }
    
    public function getConsistanceTravauxInfo($type = null)
    {
        $consistanceTravauxInfo = $this->infrastructureRepository->getConsistanceTravauxInfo($type);
        if (count($consistanceTravauxInfo) > 0) {
            return $consistanceTravauxInfo;
        }
        return false;
    }

    public function update()
    {
        $this->entityManager->flush();
    }
}