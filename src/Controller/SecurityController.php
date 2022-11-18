<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
class SecurityController extends AbstractController
{
    /**
     * @Route("/api/login", name="login")
     */
    public function loginAction(): JsonResponse
    {
        $user = $this->getUser();
           
           
            return $this->json(array(
                'username' => $user->getUsername(),
                //'roles' => $user->getRoles(),
            ));
    }
}
