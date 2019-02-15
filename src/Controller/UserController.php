<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{email}", name="user")
     * @IsGranted("ROLE_USER")
     */
    public function search(string $email): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("SELECT u FROM App\Entity\User u WHERE u.email LIKE '$email%'");
        $users = $query->execute();
        $names = [];
        foreach ($users as $user) {
            array_push($names, $user->getEmail());
        }
        return $this->json($names);
    }
}
