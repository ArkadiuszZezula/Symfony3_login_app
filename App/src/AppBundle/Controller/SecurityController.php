<?php

namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends Controller {
    
    /**
     * @Route("/login", name="login_route")
     */
    public function loginAction(Request $request) {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('home');
        }
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', array('last_username' => $lastUsername, 'error' => $error));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction() {
        session_destroy();
        return $this->redirectToRoute('homepage');
    }
     
    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction() {
    }
     
}