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
}