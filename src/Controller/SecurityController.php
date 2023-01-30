<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Service\RefreshTokenService;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/api/token/refresh", name="refresh_token")
     */
    public function refresh(Request $request, RefreshTokenService $refreshTokenService)
    {
        return $refreshTokenService->refresh($request);
    }
}
