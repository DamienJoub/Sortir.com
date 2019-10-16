<?php


namespace App\Controller;


use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends Controller {

    /**
     * @Route("/ville/list", name="ville_list")
     */
    public function liste(EntityManagerInterface $em) {
        $villes = $em -> getRepository(Ville::class) -> findAll();

        return $this -> render("ville/liste.html.twig", ["villes" => $villes]);
    }
}