<?php


namespace App\Controller;


use App\Entity\Campus;
use App\Entity\Participant;
use App\Form\GestionProfilType;
use App\Form\RegistrationType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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

        $session = $request->getSession();

        //*********************
        //Bouchon
        $session->set('identifiant', 'mm.cc@jojo.fr');
        //fin du bouchon
        //*********************

        //Si l'identifiant de l'utilisatuer est renseigné
        if($session->has('identifiant')){

            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository(Participant::class);

            $participant = new Participant();
            $participant = $repo->findByIdentifiant($session->get('identifiant'));
            $participantForm = $this->createForm(GestionProfilType::class, $participant,["participant" => $participant]);


            $participantForm->handleRequest($request);

            if($participantForm->isSubmitted() && $participantForm->isValid()){
                // si le mot de passe est saisi on le modifie en base
                if($participantForm->get('mot_de_passe')->getData() != null &&
                    $participantForm->get('confirmation')->getData()  != null &&
                    $participantForm->get('mot_de_passe')->getData() == $participantForm->get('confirmation')->getData()){
                    $participant->setMotDePasse($participantForm->get('mot_de_passe')->getData());
                }

                $participant->setNom($participantForm->get('nom')->getData());
                $participant->setPrenom($participantForm->get('prenom')->getData());
                $participant->setTelephone($participantForm->get('telephone')->getData());
                $participant->setCampus($participantForm->get('campus')->getData());
                $participant->setMail($participantForm->get('mail')->getData());
                $em->persist($participant);

                try{
                    $em->flush();
                }catch (UniqueConstraintViolationException $e){
                    return $this->redirectToRoute("monProfil");
                }

                return $this->redirectToRoute("main_home");
            }

            return $this->render("main/monProfil.html.twig",[
                "participantForm" => $participantForm->createView()
            ]);

         //sinon on retourn à l'aceuil
        }else{
            return $this->redirectToRoute("main_home");
        }
    }
}