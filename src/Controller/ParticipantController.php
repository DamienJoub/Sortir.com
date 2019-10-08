<?php


namespace App\Controller;


use App\Entity\Participant;
use App\Form\GestionProfilType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends Controller
{

    /**
     * @Route("/monProfil", name ="monProfil")
     */
    public function gestionParticipant(){
        $participant = new Participant();
        $participantForm = $this->createForm(GestionProfilType::class, $participant);


        return $this->render("main/monProfil.html.twig",[
            "participantForm" => $participantForm->createView()
        ]);
    }
}