<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/signup", name="signup")
     */
    public function index(
        AuthorizationCheckerInterface $authChecker,
        Request $request,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $encoder): Response {

        if ($authChecker->isGranted('ROLE_USER')) {
            throw new AccessDeniedException("You have to be logged out");
        }
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return new Response((string)$errors);
            } else {
                $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return new Response("Congratulations! You created an account, now you can log in.");
            }
        }
        return $this->render('registration/index.html.twig', [
            'controller_name'   => 'RegistrationController',
            'form'              => $form->createView()
        ]);
    }
}
