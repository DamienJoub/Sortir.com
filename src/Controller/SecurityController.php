<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function register(Request $request,
                             UserPasswordEncoderInterface $passwordEncoder,
                             EntityManagerInterface $em)
    {
        $participant = new Participant();
        $form = $this->createForm(RegistrationType::class, $participant);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($participant, $participant->getPassword());
            $participant->setMotDePasse($password);
            if ($participant->getAdministrateur()) {
                $participant->setRoles(['ROLE_ADMIN']);
            } else {
                $participant->setRoles(['ROLE_USER']);
            }
            $participant->setActif(true);
            $em->persist($participant);

            try{
                $em->flush();
            }catch (UniqueConstraintViolationException $e){
                $this->addFlash("danger", "Cet email existe déjà");
                return $this->redirectToRoute("register");
            }

            $this->addFlash("success", "Votre compte a bien été créé!");
            return $this->redirectToRoute("login");
        }

        return $this->render("user/register.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout")
     * @throws Exception
     */
    public function logout()
    {
        throw new Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
