<?php


namespace App\Controller;


use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends Controller {

    /**
     * @Route("/ville", name="ville")
     */
    public function liste(EntityManagerInterface $em, Request $request) {
        $newVille = new Ville();
        $villes = $em -> getRepository(Ville::class) -> findAll();

        $formVille = $this -> createForm(VilleType::class, $newVille);

        $formVille -> handleRequest($request);

        if($formVille -> isValid()) {
            $em -> persist($newVille);
            $em -> flush();

            $this -> addFlash('success', 'La ville a bien été ajoutée!');
            return $this -> redirectToRoute("ville");
        }


        return $this -> render("ville/liste.html.twig", ["villes" => $villes, "villeForm" => $formVille -> createView()]);
    }
}