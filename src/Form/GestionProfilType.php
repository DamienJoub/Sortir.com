<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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
            ->add('mot_de_passe', PasswordType::class, array(
                'mapped' => false, 'required' => false))
            ->add('confirmation', PasswordType::class, array(
                'mapped' => false, 'required' => false
            ))
            ->add('campus',EntityType::class, ["class"=>Campus::class, "choice_label"=>"nom", "label" => "Campus", "data" => $participant->getCampus()])
            ->add('nomPhotoProfil', FileType::class, [
                'label' => 'Photo de profil',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // everytime you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/png',
                            'image/gif',
                            'image/x-icon',
                            'image/jpeg',

                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide. (png, gif, ico ou jpeg)',
                    ])
                ],
            ])
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
