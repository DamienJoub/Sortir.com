<?php


namespace App\Controller;


use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\GestionProfilType;
use App\Form\RegistrationType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function gestionParticipant(Request $request, UserPasswordEncoderInterface $passwordEncoder){

        //Si l'identifiant de l'utilisatuer est renseigné
        if($this->getUser() != null){

            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository(Participant::class);

            $participant = new Participant();
            $participant = $repo->findByIdentifiant($this->getUser()->getMail());
            $participantForm = $this->createForm(GestionProfilType::class, $participant,["participant" => $participant]);

            $participantForm->handleRequest($request);

            if($participantForm->isSubmitted() && $participantForm->isValid()){
                // si le mot de passe est saisi on le modifie en base
                if($participantForm->get('mot_de_passe')->getData() != null &&
                    $participantForm->get('confirmation')->getData()  != null &&
                    $participantForm->get('mot_de_passe')->getData() == $participantForm->get('confirmation')->getData()){
                    $participant->setMotDePasse($passwordEncoder->encodePassword($participant, $participantForm->get('mot_de_passe')->getData()));
                }

                /** UploadedFile $myFile */
                $myFile = $participantForm['nomPhotoProfil']->getData();

                // Cette condition est nécessaire car le fichier n'est pas obligatoire
                // donc on fais toute ces histoires que si il y a un fichier
                if ($myFile) {
                    $originalFilename = pathinfo($myFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$myFile->guessExtension();

                    // Move the file to the directory where files are stored
                    try {
                        $myFile->move(
                            $this->getParameter('file_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $participantForm->get('file')->addError($e);
                        throw new FileException("Erreur lors de l'upload du fichier",0,$e);
                    }

                    $participant->setNomPhotoProfil($newFilename);
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
                    $this->addFlash("danger", "Cette addresse e-mail est déjà prise par un autre utilisateur.");
                    return $this->redirectToRoute("monProfil", ["profil" => $participant]);
                }

                $this->addFlash('success', "Votre profil à été mis à jour.");
                return $this->redirectToRoute("main_home");
            }

            return $this->render("main/monProfil.html.twig",[
                "participantForm" => $participantForm->createView(),
                "profil" => $participant
            ]);

         //sinon on retourn à l'aceuil
        }else{
            return $this->redirectToRoute("main_home");
        }
    }

    /**
     * @Route("/profil/{id}", name = "profil", requirements={"id"="\d+"})
     * @param int $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function afficherProfil($id = -1, EntityManagerInterface $em){
        if($id >0){
            $participant = $em->getRepository(Participant::class)->find($id);
            return $this->render("user/profile.html.twig", ["profil" => $participant]);
        }else{
            return $this->redirectToRoute("main_home");
        }
    }

    /**
     * @Route("/allparticipants", name="allparticipants")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function getAllParticipants(EntityManagerInterface $em) {
        $participants = $em->getRepository(Participant::class)->findAll();

        return $this->render("user/liste.html.twig", ["participants" => $participants]);
    }

    /**
     * @Route("/setinactif/{id}", name="setinactif", requirements={"id"="\d+"})
     * @param Participant $participant
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function setInactif(Participant $participant, EntityManagerInterface $em) {

        if($participant->getActif() == true) {
            $participant->setActif(false);
        } else {
            $participant->setActif(true);
        }

        $em->persist($participant);
        $em->flush();

        return $this->redirectToRoute("allparticipants");
    }

    /**
     * @Route("/deleteprofil/{id}", name="delete_profil", requirements={"id"="\d+"})
     * @param Participant $participant
     * @param EntityManagerInterface $em
     */
    public function deleteProfil(Participant $participant, EntityManagerInterface $em) {
        /*$sortiesOrga = $em->getRepository(Sortie::class)->findByParticipantO($participant->getId());

        // Les sorties organisé pas le participant sont supprimées
        foreach ($sortiesOrga as $sortie) {
            $sortie->setParticipantsP(null);
            $em->remove($sortie);
        }

        // Supprimé sont inscription aux sorties
        $sorties = $em->getRepository(Sortie::class)->findAll();
        foreach ($sorties as $sortie) {
            $listeParticipants = $em -> getRepository(Participant::class) -> findBySortie($sortie);
            // Si le participant est inscrit à la sortie
            if(in_array($this->getUser(), $listeParticipants)) {
                //\unset($listeParticipants[$this->getUser()]);
                unset($listeParticipants[$this->getUser()]);
            }
        }*/
    }
}