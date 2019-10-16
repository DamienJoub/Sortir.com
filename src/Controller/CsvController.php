<?php


namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Command\Command;
use League\Csv\Reader;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CsvController extends Controller
{
    /**
     * @return void
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function execute(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {

        $reader = Reader::createFromPath('%kernel.root.dir%/../uploads/listParticipants.csv');
        $reader->setDelimiter(';');
        $results = $reader->fetchAssoc();
        foreach ($results as $row) {

            $participant = new Participant();
            $participant->setMail($row['mail']);
            if($em->getRepository(Participant::class)->findByMail($participant->getMail()) == null){
                $participant->setNom($row['nom']);
                $participant->setPrenom($row['prenom']);
                $participant->setTelephone($row['telephone']);
                $password = $passwordEncoder->encodePassword($participant, $row['mot_de_passe']);
                $participant->setMotDePasse($password);
                $participant->setAdministrateur($row['administrateur']);
                $participant->setActif($row['actif']);
                $participant->setCampus($em -> getRepository(Campus::class) -> findOneByNom($row['campus']));

                if ($participant->getAdministrateur()) {
                    $participant->setRoles(['ROLE_ADMIN']);
                } else {
                    $participant->setRoles(['ROLE_USER']);
                }

                $em->persist($participant);
                $em->flush();
            }
        }
    }
}