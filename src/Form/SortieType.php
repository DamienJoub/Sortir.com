<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $campus = $options["campus"];
        $builder
            -> add('nom', TextType::class, ["label" => "Nom"])
            -> add('date_debut', DateTimeType::class, [
                "label" => "Date et heure de début",
                "data" => new \DateTime(),
                "format" => "dd/MM/yyyy HH:mm",
                "html5" => false,
                "widget" => "single_text",
                "attr" => ["class" => "js-datepicker"]
            ])
            -> add('duree', IntegerType::class, ["label" => "Durée (min)"])
            -> add('date_cloture', DateTimeType::class, [
                "label" => "Date et heure de limite d'inscription",
                "data" => new \DateTime(),
                "format" => "dd/MM/yyyy HH:mm",
                "widget" => "single_text",
                "html5" => false,
                "attr" => ["class" => "js-datepicker"]
                ])
            -> add('nb_inscription_max', IntegerType::class, ["label" => "Nombre de places"])
            -> add('infos_sortie', TextareaType::class, ["label" => "Description et infos", "required" => false])
            -> add('campus', TextType::class, ["label" => "Campus", "data" => $campus -> getNom(), "attr" => ["readonly" => true]])
            -> add("lieu", EntityType::class, [
                "class" => Lieu::class,
                "group_by" => "ville.nom",
                "placeholder" => "Choisissez un lieu",
                "label" => "Lieu",
                "choice_label" => "nom",
                'query_builder' => function (EntityRepository $er) {
                    return $er -> createQueryBuilder('c') -> orderBy('c.nom', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver -> setRequired("campus");
        $resolver -> setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
