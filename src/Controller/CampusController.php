<?php


namespace App\Controller;


use App\Entity\Campus;
use App\Form\CampusType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends Controller {

    /**
     * @Route("/campus/{id}", name="campus")
     */
    public function liste(EntityManagerInterface $em, Request $request, $id = 0) {
        $newCampus = ($id > 0) ? $em -> getRepository(Campus::class) -> find($id) : new Campus();
        $campus = $em -> getRepository(Campus::class) -> findAll();

        $searchCampus = $request -> request -> get("search_campus");

        $filteredCampus = array();

        if($searchCampus) {
            foreach ($campus as $campu) {
                if(stripos($campu -> getNom(), $searchCampus) !== false) {
                    array_push($filteredCampus, $campu);
                }
            }
        }
        else {
            $filteredCampus = $campus;
        }

        $formCampus = $this -> createForm(CampusType::class, $newCampus);

        $formCampus -> handleRequest($request);

        if($formCampus -> isSubmitted() && $formCampus -> isValid()) {
            $em -> persist($newCampus);
            $em -> flush();

            $id > 0 ?
                $this -> addFlash('success', 'Le campus a bien été modifié !')
                :
                $this -> addFlash('success', 'Le campus a bien été ajouté !');
            return $this -> redirectToRoute("campus");
        }

        return $this -> render("campus/liste.html.twig", ["campus" => $filteredCampus, "campusForm" => $formCampus -> createView()]);
    }

}