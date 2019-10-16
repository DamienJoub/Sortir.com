<?php


namespace App\Controller;


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
    public function ajoutMultiple(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder){
        $this->execute($em, $passwordEncoder);
    }
}