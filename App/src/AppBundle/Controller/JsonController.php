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
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class JsonController extends Controller
{
    /**
     * @Route("/jsonCreate/{urlJson}", name="jsonCreate")
     */
    public function registerJsonAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, $urlJson)
    {
        
        $json = file_get_contents($urlJson, true);
        $jsonDecode = (json_decode($json, true));
        $jsonUsername = $jsonDecode['username'];
        $jsonEmail = $jsonDecode['email'];
        $jsonPassword = $jsonDecode['plainPassword'];        
        
        $user = new User();
        $user->setUsername($jsonUsername);
        $user->setEmail($jsonEmail);
        $user->setPlainPassword($jsonPassword);
        $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        
        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new Response($errorsString);
        } else {
            $form = $this->createForm(UserType::class, $user);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('login_route');
        }
    }
    
    
    /**
     * @Route("/jsonRead/{id}", name="jsonRead")
     */
    public function readJsonAction ($id) {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repository->find($id);
        $jsonContent = $serializer->serialize($user, 'json');
        return new Response($jsonContent);
    }
    
    
    /**
     * @Route("/jsonUpdate/{id}/{urlJson}", name="jsonUpdate")
     */
    public function updateJsonAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, $urlJson, $id) {
        $json = file_get_contents($urlJson, true);
        $jsonDecode = (json_decode($json, true));
        $jsonUsername = $jsonDecode['username'];
        $jsonEmail = $jsonDecode['email'];
        $jsonPassword = $jsonDecode['plainPassword'];    
        
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repository->find($id);
        
        if($jsonUsername !== "") {
            $user->setUsername($jsonUsername);
        }
        if($jsonEmail !== "") {
            $user->setEmail($jsonEmail);
        }
        if($jsonPassword !== "") {
            $user->setPlainPassword($jsonPassword);
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
        }
        
        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new Response($errorsString);
        } else {
            $form = $this->createForm(UserType::class, $user);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
    }
    
    
    /**
     * @Route("/jsonDelete/{id}", name="jsonDelete")
     */
    public function deleteJsonAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, $id) {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repository->find($id);
        if(!$user) {
            return new Response ("User not exists");
        }        
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('homepage');
    }
    
}