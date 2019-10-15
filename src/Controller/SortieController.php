<?php


namespace App\Controller;


use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SortieController
 * @package App\Controller
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends Controller {

    /**
     * @Route("/filtre", name="filtre")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */
    public function filtre(EntityManagerInterface $em, Request $request) {

        $sorties = $em -> getRepository(Sortie::class) -> findAll();
        $sortiesFiltrees = array();
        $filtreCampus = $request->request->get('campus');
        $filtreSearch = $request->request->get('search');
        $filtreDateDebut = $request->request->get('dateDebut');
        $filtreDateFin = $request->request->get('dateFin');
        $filtreOrganisateur = $request->request->get('organisateur');
        $filtreInscrit = $request->request->get('inscrit');
        $filtreNonInscrit = $request->request->get('nonInscrit');
        $filtrePassee = $request->request->get('passee');

        foreach ($sorties as $sortie) {
            if($sortie->getCampus() != $filtreCampus) {
                // soit ajouter puis enlever les duplicate soit enlever direct
            }
            /*if() {
                array_push($sortiesFiltrees, $sortie);
            }*/
        }

        /*$data = $request->request->get('search');

        $query = $em->createQuery(
            'SELECT * FROM sortie:Suplier s
                WHERE s.nom LIKE \'%:data%\'')
            ->setParameter('data',$data);

        $res = $query->getResult();

        return $this->render('sortie/liste.html.twig', array(
            'res' => $res));*/
    }

    /**
     * @Route("/liste", name="liste")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function liste(EntityManagerInterface $em) {

        $sorties = $em -> getRepository(Sortie::class) -> findAll();
        $campus = $em -> getRepository(Campus::class) -> findAll();

        $isInscrit = array();

        $dateDuJour = new DateTime();
        $dateDuJour1 = new DateTime();
        $dateDuJour1 = $dateDuJour1->modify('+1 day');

        foreach ($sorties as $sortie) {
            $listeParticipants = $em -> getRepository(Participant::class) -> findBySortie($sortie);
            if(in_array($this->getUser(), $listeParticipants)) {
                array_push($isInscrit,$sortie);
            }
        }

        return $this -> render("sortie/liste.html.twig",
            ["sorties" => $sorties, "isInscrit" => $isInscrit, "campus" => $campus,
                "dateDuJour" => $dateDuJour, "dateDuJour1" => $dateDuJour1]);
    }

    /**
     * @Route("/creer", name="creer")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(EntityManagerInterface $em, Request $request) {
        $sortie = new Sortie();


        $form = $this -> createForm(SortieType::class, $sortie, ["campus" => $this -> getUser() -> getCampus()]);

        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()) {
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
     * @Route("/detail/{id}", name ="detail", requirements={"id"="\d+"})
     * @param int $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
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
     * @Route("/inscription/{id}" , name ="inscription", requirements={"id"="\d+"})
     */
    public function inscription($id = -1, EntityManagerInterface $em, Request $request){
        if($id > 0) {
            $sortie = $em->getRepository(Sortie::class)->find($id);
            if ($sortie->getDateCloture() > new DateTime("now") && $sortie->getEtat()->getLibelle() == 'Ouverte') {
                $participants = $sortie->getParticipantsP()->toArray();
                if (!in_array($this->getUser() , $participants)) {
                    array_push($participants, $this->getUser());
                }
                $sortie->setParticipantsP($participants);
                $em->persist($sortie);
                $em->flush();
            }
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param int $id
     * @param EntityManagerInterface $em
     * @param Request $request
     * @Route("/desinscription/{id}", name = "desinscription", requirements={"id"="\d+"})
     */
    public function desinscription($id = -1, EntityManagerInterface $em, Request $request){
        if($id > 0){
            $sortie = $em->getRepository(Sortie::class)->find($id);
            $participants = $sortie->getParticipantsP()->toArray();
            if(in_array($this->getUser() , $participants) && $sortie->getDateDebut() > new DateTime("now")){
                unset($participants[array_search($this->getUser(), $participants)]);
            }
            $sortie->setParticipantsP($participants);
            $em->persist($sortie);
            $em->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param int $id
     * @param EntityManagerInterface $em
     * @param Request $request
     * @Route("/publication/{id}" , name  = "publication" ,  requirements={"id"="\d+"})
     */
    public function publication($id = -1, EntityManagerInterface $em, Request $request){
        if ($id > 0){
            $sortie = $em->getRepository(Sortie::class)->find($id);
            if($sortie->getParticipantO() == $this->getUser() && $sortie->getEtat()->getLibelle() == 'Créée' && $sortie->getDateDebut() > new DateTime("now")){
                var_dump('gg');
                $etat = $em->getRepository(Etat::class)->find(2);
                $sortie->setEtat($etat);
                $em->persist($sortie);
                $em->flush();
            }
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param int $id
     * @param EntityManagerInterface $em
     * @param Request $request
     * @Route("/annuler/{id}" , name ="annuler" , requirements={"id"="\d+"})
     */
    public function annuler($id =-1, EntityManagerInterface $em, Request $request){
        if ($id > 0){
            $sortie = $em->getRepository(Sortie::class)->find($id);
            if($sortie->getParticipantO() == $this->getUser() &&
                ($sortie->getEtat()->getLibelle() == 'Créée' || $sortie->getEtat()->getLibelle() == 'Ouverte') &&
                $sortie->getDateDebut() > new DateTime("now")){
                $sortie->setEtat($em->getRepository(Etat::class)->find(5));
                $sortie->setParticipantsP(null);
                $em->persist($sortie);
                $em->flush();
            }
        }
        return $this->redirect($request->headers->get('referer'));
    }
}