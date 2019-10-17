<?php


namespace App\Controller;


use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends Controller {

    /**
     * @Route("/ville/{id}", name="ville")
     */
    public function liste(EntityManagerInterface $em, Request $request, $id = 0) {
        $newVille = ($id > 0) ? $em -> getRepository(Ville::class) -> find($id) : new Ville();
        $villes = $em -> getRepository(Ville::class) -> findAll();

        $searchVille = $request -> request -> get("search_ville");

        $filteredVille = array();

        if($searchVille) {
            foreach ($villes as $ville) {
                if(stripos($ville -> getNom(), $searchVille) !== false) {
                    array_push($filteredVille, $ville);
                }
            }
        }
        else {
            $filteredVille = $villes;
        }

        $formVille = $this -> createForm(VilleType::class, $newVille);

        $formVille -> handleRequest($request);

        if($formVille -> isSubmitted() && $formVille -> isValid()) {
            $em -> persist($newVille);
            $em -> flush();

            $id > 0 ?
                $this -> addFlash('success', 'La ville a bien été modifiée !')
            :
                $this -> addFlash('success', 'La ville a bien été ajoutée !');
            return $this -> redirectToRoute("ville");
        }

        return $this -> render("ville/liste.html.twig", ["villes" => $filteredVille, "villeForm" => $formVille -> createView()]);
    }

    /**
     * @Route("/ville/delete/{id}", name="ville_delete")
     */
    public function delete(EntityManagerInterface $em, Ville $ville) {

        $lieux = $em -> getRepository(Lieu::class) -> findByVille($ville -> getId());

        foreach($lieux as $lieu) {
            $sorties = $em -> getRepository(Sortie::class) -> findByLieu($lieu-> getId());
            if($sorties) {
                foreach ($sorties as $sortie) {
                    $sortie -> setParticipantsP(null);
                    $em -> remove($sortie);
                }
            }
            $em -> remove($lieu);
        }

        $em -> remove($ville);
        $em -> flush();

        $this -> addFlash('success', 'La ville est ses éléments associés ont bien été supprimés!');
        return $this -> redirectToRoute('ville');
    }
}