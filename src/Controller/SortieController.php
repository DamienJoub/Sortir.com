<?php


namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends Controller {

    /**
     * @Route("/sortie/list", name="sortie_liste")
     */
    public function liste(EntityManagerInterface $em) {

        $sorties = $em -> getRepository(Sortie::class) -> findAll();

        $isInscrit = array();

        foreach ($sorties as $sortie) {
            $listeParticipants = $em -> getRepository(Participant::class) -> findBySortie($sortie);
            if(in_array($this->getUser(), $listeParticipants)) {
                array_push($isInscrit,$sortie);
            }
        }

        return $this -> render("sortie/liste.html.twig", ["sorties" => $sorties, "isInscrit" => $isInscrit]);
    }

    /**
     * @Route("/sortie/creer", name="sortie_creer")
     */
    public function create(EntityManagerInterface $em, Request $request) {
        $sortie = new Sortie();


        $form = $this -> createForm(SortieType::class, $sortie, ["campus" => $this -> getUser() -> getCampus()]);

        $form -> handleRequest($request);

        if($form -> isSubmitted()) {
            $sortie -> setParticipantO($this -> getUser());
            $sortie -> setCampus($this -> getUser() -> getCampus());
            $sortie -> setEtat($em -> getRepository(Etat::class) -> findOneByLibelle("Créée"));

            $em -> persist($sortie);
            $em -> flush();

            $this->addFlash('success', 'La sortie a bien été ajoutée!');
            return $this->redirectToRoute('sortie_liste');
        }

        return $this -> render("sortie/add.html.twig", ["sortieForm" => $form -> createView()]);
    }

    /**
     * @Route("/sortie/detail/{id}", name ="detail_sortie", requirements={"id"="\d+"})
     */
    public function detail($id = -1, EntityManagerInterface $em){
        if($id > 0){
            $sortie = $em ->getRepository(Sortie::class) ->find($id);
            $participants = $em->getRepository(Participant::class) ->findBySortie($sortie);
            return $this->render("sortie/detail.html.twig", ["sortie" => $sortie, "participants" => $participants, "id" => $id]);
        }else{
            return $this->redirectToRoute("main_home");
        }
    }

    /**
     * @param int $id
     * @param EntityManagerInterface $em
     * @Route("/sortie/inscription/{id}" , name ="inscription_sortie", requirements={"id"="\d+"})
     */
    public function inscription($id = -1, EntityManagerInterface $em, Request $request){
        if($id > 0) {
            $sortie = new Sortie();
            $sortie = $em->getRepository(Sortie::class)->find($id);
            if ($sortie->getDateCloture() > new DateTime("now")) {
                $participants = $sortie->getParticipantsP()->toArray();
                if(!in_array($participants, [$this->getUser()])){
                    array_push($participants, $this->getUser());
                }
                $sortie->setParticipantsP($participants);
                $em->persist($sortie);
                $em->flush();
                return $this->redirect($request->headers->get('referer'));
            } else {
                return $this->redirectToRoute("main_home");
            }
        }
    }
}