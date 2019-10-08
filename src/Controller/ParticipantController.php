<?php


namespace App\Controller;


use App\Entity\Participant;
use App\Form\GestionProfilType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ParticipantController extends Controller
{

    /**
     * @Route("/monProfil", name ="monProfil")
     */
    public function gestionParticipant(Request $request){

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Participant::class);

        $participant = new Participant();
        $participantForm = $this->createForm(GestionProfilType::class, $participant);

        $participant = $repo->findByIdentifiant('mm.cc@jojo.fr');
        $participantForm = $this->createForm(GestionProfilType::class, $participant,["participant" => $participant]);
        $participantForm->handleRequest($request);

        if($participantForm->isSubmitted() && $participantForm->isValid()){
            return $this->redirectToRoute("main_home");
        }

        var_dump($participant->getCampus()->getNom());
        return $this->render("main/monProfil.html.twig",[
            "participantForm" => $participantForm->createView()
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function register(Request $request,
                             UserPasswordEncoderInterface $passwordEncoder,
                             EntityManagerInterface $em)
    {
        $participant = new Participant();
        $form = $this->createForm(RegistrationType::class, $participant);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($participant, $participant->getMotDePasse());
            $participant->setMotDePasse($password);
            $participant->setRoles(['ROLE_USER']);
            $em->persist($participant);
            $em->flush();

            $this->addFlash("success", "Votre compte a bien été créé!");
            return $this->redirectToRoute("login");
        }

        return $this->render("participant/register.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('participant/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){}
}