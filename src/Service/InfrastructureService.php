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
        return 0;
    }

    public function getAllNiveauInfrastructureByDomaine($domaine = null)
    {
        $niveauxInfrastructures = $this->infrastructureRepository->getAllNiveauInfrastructureByDomaine($domaine);
        if (count($niveauxInfrastructures) > 0) {
            return $niveauxInfrastructures;
        }
        return 0;
    }

    public function getAllNiveauInfrastructureByDomaineNiveau3($domaine = null)
    {
        $niveauxInfrastructures = $this->infrastructureRepository->getAllNiveauInfrastructureByDomaineNiveau3($domaine);
        if (count($niveauxInfrastructures) > 0) {
            return $niveauxInfrastructures;
        }
        return 0;
    }

    public function getAllSourceInfo()
    {
        $sourceInformations = $this->infrastructureRepository->getAllSourceInfo();
        if (count($sourceInformations) > 0) {
            return $sourceInformations;
        }
        return 0;
    }

    public function getAllIndicatifNiveau3()
    {
        $indicatifNiveau3 = $this->infrastructureRepository->getAllIndicatifNiveau3();
        if (count($indicatifNiveau3) > 0) {
            return $indicatifNiveau3;
        }
        return 0;
    }
    
    public function getAllIndicatifNiveau2()
    {
        $indicatifNiveau3 = $this->infrastructureRepository->getAllIndicatifNiveau2();
        if (count($indicatifNiveau3) > 0) {
            return $indicatifNiveau3;
        }
        return 0;
    }

    public function getAllPrestataireInfo()
    {
        $prestatairesInfo = $this->infrastructureRepository->getAllPrestataireInfo();
        if (count($prestatairesInfo) > 0) {
            return $prestatairesInfo;
        }
        return 0;
    }

    
    public function update()
    {
        $this->entityManager->flush();
    }
}