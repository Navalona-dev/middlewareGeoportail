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
        $result = $this->educationRepository->addInfrastructureEducation($data['nom'], $data['indicatif'], $data['categorie'], $data['localite'], $data['sourceInformation'], $data['modeAcquisitionInformation'], $data['communeTerrain'], $data['numeroSequence'], (int) $data['codeProduit'], (int) $data['codeCommune'], (float) $data['latitude'],(float) $data['longitude']);
        return $result;
    }

    public function getAllInfrastructuresEducation()
    {
        $educations = $this->educationRepository->getAllInfrastructuresEducation();
        if (count($educations) > 0) {
            return $educations;
        }
        return 0;
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