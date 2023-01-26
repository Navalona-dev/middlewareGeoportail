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
        $result = $this->routeRepository->addInfrastructureRoute($data['categorie'], $data['localite'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['communeTerrain'], $data['pk']['debut'], $data['section'], $data['numeroRoute'], $data['gestionnaire'], $data['modeGestion'], null,  $data['pk']['fin'], null, $data['largeur']['hausse'], $data['largeur']['accotement'], $data['structure'], $data['region'], $data['district'], $data['gps'], $data['longitude'], $data['latitude']);
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
        return 0;
    }

    public function getAllInfrastructuresBaseRoute()
    {
        $routes = $this->routeRepository->getAllInfrastructuresBaseRoute();
        if (count($routes) > 0) {
            return $routes;
        }
        return 0;
    }
    
    
    public function update()
    {
        $this->entityManager->flush();
    }

    public function remove($permission)
    {
        $this->entityManager->remove($permission);
        $this->update();
    }

}