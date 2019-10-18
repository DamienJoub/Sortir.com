<?php


namespace App\Controller;


use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LieuController extends Controller
{
    /**
     * @Route('/ajoutLieu' name='ajoutLieu')
     */
    public function ajoutLieu(EntityManagerInterface $em, Request $request){

        $lieu = new Lieu();


        $form = $this -> createForm(LieuType::class, $lieu, ["lieu" => new Lieu()]);

        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()) {
            $lieu -> setParticipantO($this -> getUser());
            $lieu -> setCampus($this -> getUser() -> getCampus());
            $lieu -> setEtat($em -> getRepository(Etat::class) -> findOneByLibelle("Créée"));

            $em -> persist($lieu);
            $em -> flush();

            $this->addFlash('success', 'La sortie a bien été ajoutée!');
            return $this->redirectToRoute('sortie_detail', array("id" => $lieu -> getId()));
        }

        return $this -> render("sortie/add.html.twig", ["sortieForm" => $form -> createView()]);
    }

}