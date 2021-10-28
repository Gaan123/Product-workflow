<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
class HomeController extends AbstractController
{
    /**
     * @Route("/sdsd")
     */
    public function number()
    {
        dd(1234);
    }
    /**
     * @Route("/api/signup"),methods={"post"})
     */
    public function signup(DocumentManager $dm,Request $request,UserPasswordHasherInterface $passwordHasher)
    {
        $requestData=$request->request->all();
        $user = new User();
        $user->setName($requestData['name']);
        $user->setEmail($requestData['email']);
        $user->setRole($requestData['role']);
//        $encoder = $this->container->get('security.password_hasher');
        $user->setUsername($requestData['username']);
        $user->setPassword($passwordHasher->hashPassword($user,$requestData['password']));

        $dm->persist($user);
        $dm->flush();

        return new Response('Created product id ' );
    }
}
