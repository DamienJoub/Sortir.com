<?php


namespace App\Controller;


use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
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
        return $this -> render("sortie/liste.html.twig", ["sorties" => $sorties]);
    }

    /**
     * @Route("/sortie/creer", name="sortie_creer")
     */
    public function create(EntityManagerInterface $em, Request $request) {
        $sortie = new Sortie();

        $participant = $em -> getRepository(Participant::class) -> find(1);

        $form = $this -> createForm(SortieType::class, $sortie, ["participant" => $participant]);

        $form -> handleRequest($request);

        if($form -> isSubmitted()) {

        }

        return $this -> render("sortie/add.html.twig", ["sortieForm" => $form -> createView()]);
    }
}