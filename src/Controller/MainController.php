<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends Controller
{
    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        //return $this->render("main/home.html.twig");
        return $this->redirectToRoute("sortie_liste");
    }

    /**
     * @Route("/", name="main_home")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function main_home(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute("sortie_liste");
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
}