<?php


namespace App\Controller;


use App\Entity\Participant;
use App\Form\GestionProfilType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends Controller
{

    /**
     * @Route("/monProfil", name ="monProfil")
     */
    public function gestionParticipant(Request $request){

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Participant::class);

        $participant = new Participant();

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