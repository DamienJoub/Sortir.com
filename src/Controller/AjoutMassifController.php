<?php


namespace App\Controller;


use App\Entity\FichierCSV;
use App\Form\FichierCSVType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AjoutMassifController extends CsvController
{
    /**
     * @param EntityManagerInterface $em
     * @param Request $request
     * @Route("/ajoutMultiple", name="ajoutMultiple")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function ajoutMultiple(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder,Request $request){

        $csv = new FichierCSV();
        $form = $this->createForm(FichierCSVType::class, $csv);
        $form->handleRequest($request);
        if(!$form->isSubmitted()){
            return $this->render("user/csv.html.twig",[
                "csvForm" => $form->createView()
            ]);
        }
//        $file= $request->files->get('inputfile');
        if($form['file']->getData() != null){
            $file = $form['file']->getData();
            $file->move( $this->getParameter('file_csv'), 'listParticipants.csv');
            $this->execute($em, $passwordEncoder);
        }
        return $this->redirectToRoute("main_home");
    }
}