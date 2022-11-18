<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Service\CreateMediaObjectAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route("/useradd", name="adduser")
     */
    public function addUser(Request $request , UserPasswordEncoderInterface $passwordEncoder) {
        $user = $this->getUser();
        //if(in_array("ROLE_CREATE_USER", $user->getRoles())){
            $data = array();
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
               
                $em = $this->getDoctrine()->getManager();
                $data = json_decode($request->getContent(), true);
                $roles = (array) $data['roles'];
                $user = new User();
               
                $user->setName($data['name']);
                $user->setUsername($data['username']);
               
                $user->setPassword($passwordEncoder->encodePassword($user, $data['password']));
                $user->setEmail($data['email']);
                /*
                if(sizeof($data['roles']) > 0) {
                    $user->setRoles($data['roles']);
                }*/
               
                $em->persist($user);
                $em->flush();
                $response = new Response();
    
                $encoders = [new XmlEncoder(), new JsonEncoder()];
                $normalizers = [new ObjectNormalizer()];
                $serializer = new Serializer($normalizers, $encoders);
                $jsonContent = $serializer->serialize($user, 'json');
                $response->setContent(json_encode([
                    'status' => true,
                    'user' => $user->getUsername(),
                    'data' => json_decode($jsonContent),
                ]));
    
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(Response::HTTP_OK );
            
            // $response = new JsonResponse(['data' => 123]);
                return $response;
            }
        /*} else {
            $response = new Response();
            $response->setContent(json_encode([
                'status' => false,
                'user' => $user->getUsername(),
                'message' => "Acces denied",
            ]));

            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK );*/
        
        // $response = new JsonResponse(['data' => 123]);
         /*   return $response;
        }*/
        
            
    }
}
