<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GestionProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $participant = $options["participant"];
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail')
            ->add('mot_de_passe',PasswordType::class, array('required' => false))
            ->add('confirmation', PasswordType::class, array(
                'mapped' => false, 'required' => false
            ))
            ->add('campus',TextType::class, ["label" => "Campus", "data" => $participant->getCampus()->getNom()])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver -> setRequired("participant");
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
