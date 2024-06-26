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
use App\Repository\LocalisationInfrastructureRepository;

class LocalisationInfrastructureService
{
    private $tokenStorage;
    private $entityManager;
    private $session;
    private $LocalisationInfrastructureRepository;

    public function __construct(TokenStorageInterface  $TokenStorageInterface, EntityManager $entityManager, SessionInterface $session, LocalisationInfrastructureRepository $LocalisationInfrastructureRepository)
    {
        $this->tokenStorage = $TokenStorageInterface;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->LocalisationInfrastructureRepository = $LocalisationInfrastructureRepository;
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

    public function getAllRegions()
    {
        $regionsInfrastructures = $this->LocalisationInfrastructureRepository->getAllRegions();
        if (count($regionsInfrastructures) > 0) {
            return $regionsInfrastructures;
        }
        return false;
    }

    public function getInfoRegion($region)
    {
        $regionsInfrastructures = $this->LocalisationInfrastructureRepository->getInfoRegion($region);
        if (count($regionsInfrastructures) > 0) {
            return $regionsInfrastructures;
        }
        return false;
    }

    public function getCoordonneeRegion()
    {
        $regionsInfrastructures = $this->LocalisationInfrastructureRepository->getCoordonneeRegion();
        if (count($regionsInfrastructures) > 0) {
            return $regionsInfrastructures;
        }
        return false;
    }

    public function getAllDistricts()
    {
        $districtsInfrastructures = $this->LocalisationInfrastructureRepository->getAllDistricts();
        if (count($districtsInfrastructures) > 0) {
            return $districtsInfrastructures;
        }
        return false;
    }

    public function getAllCommunes()
    {
        $communessInfrastructures = $this->LocalisationInfrastructureRepository->getAllCommunes();
        if (count($communessInfrastructures) > 0) {
            return $communessInfrastructures;
        }
        return false;
    }

    public function getAllLocalites()
    {
        $localitesInfrastructures = $this->LocalisationInfrastructureRepository->getAllLocalites();
        if (count($localitesInfrastructures) > 0) {
            return $localitesInfrastructures;
        }
        return false;
    }

    public function getAllLocalitesInstat()
    {
        $localitesInfrastructures = $this->LocalisationInfrastructureRepository->getAllLocalitesInstat();
        if (count($localitesInfrastructures) > 0) {
            return $localitesInfrastructures;
        }
        return false;
    }

    public function getAllCoordonneLocalites()
    {
        $localitesInfrastructures = $this->LocalisationInfrastructureRepository->getAllCoordonneLocalites();
        if (count($localitesInfrastructures) > 0) {
            return $localitesInfrastructures;
        }
        return false;
    }
    
    public function getAllDistrictByRegion($region = null)
    {
        $districtsInfrastructure = $this->LocalisationInfrastructureRepository->getAllDistrictByRegion($region);
        if (count($districtsInfrastructure) > 0) {
            return $districtsInfrastructure;
        }
        return false;
    }

    public function getAllCommunesByDistrictInRegion($region = null, $district = null)
    {
        $communesInfrastructure = $this->LocalisationInfrastructureRepository->getAllCommunesByDistrictInRegion($region, $district);
        if (count($communesInfrastructure) > 0) {
            return $communesInfrastructure;
        }
        return false;
    }

    public function getAllLocalitesInCommunes($codeCommune = null)
    {
        $localitesInfrastructure = $this->LocalisationInfrastructureRepository->getAllLocalitesInCommunes($codeCommune);
        if (count($localitesInfrastructure) > 0) {
            return $localitesInfrastructure;
        }
        return false;
    }

    public function update()
    {
        $this->entityManager->flush();
    }

    /*public function remove($permission)
    {
        $this->entityManager->remove($permission);
        $this->update();
    }

    public function getAllPermissions()
    {
        $permissions = $this->entityManager->getRepository(Permission::class)->findAll();
        if (count($permissions) > 0) {
            return $permissions;
        }
        return 0;
    }

    public function getAllPermissionByCategorie($categorie)
    {
        $permissions = $this->entityManager->getRepository(Permission::class)->findBy(['categoryofpermission' => $categorie]);
        if (count($permissions) > 0) {
            return $permissions;
        }
        return 0;
    }

    public function getPermissionById($id)
    {
        $permission = $this->entityManager->getRepository(Permission::class)->find($id);
        if ($permission) {
            return $permission;
        }
        return null;
    }

    public function addPrivilege($permission, $privilege)
    {
        $permission->addPrivilege($privilege);
    }

    public function removePrivilege($privilege, $permission)
    {
        $permission->removePrivilege($privilege);
    }*/

}