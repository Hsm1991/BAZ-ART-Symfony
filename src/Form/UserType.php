<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('lastname')
            ->add('firstname')
            ->add('address')
            ->add('tel')
            ->add('city',ChoiceType::class, [
                'choices' => [
                    'Tunis' => 'Tunis',
                    'Ariana' => 'Ariana',
                    'Ben Arous' =>'Ben Arous',
                    'Manouba' =>'Manouba',
                    'Sousse' =>'Sousse',
                    'Tataouine' =>'Tataouine',
                    'Nabeul' =>'Nabeul',
                    'Autre' =>'Autre',


                ],

                ])
//            ->add('roles', ChoiceType::class, [
//                'choices' => [
//                    'Artiste' => 'ROLE_ARTISTE',
//                    'Super Admin' => 'ROLE_SUPER_ADMIN',
//                    'Prestataire' => 'ROLE_PRESTATAIRE',
//                    'Simple utilisateur' => 'ROLE_USER'
//
//                ],
//
//                'multiple'  => true, ])
//            ->add('roles')
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmation du mot de passe'),
            ))
            ->add('isVerified')
            ->add('category')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
