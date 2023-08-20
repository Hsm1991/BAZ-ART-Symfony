<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegisterFrontType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('address')
            ->add('city',ChoiceType::class, [
                'choices' =>  array(
                    'Tunis' => 'Tunis',
                    'Ariana' => 'Ariana',
                    'Ben Arous' =>'Ben Arous',
                    'Manouba' =>'Manouba',
                    'Sousse' =>'Sousse',
                    'Tataouine' =>'Tataouine',
                    'Nabeul' =>'Nabeul',
                    'Autre' =>'Autre',
                    )
                ,

                'expanded'  => false,
                'multiple'  => false,

            ])





            ->add('tel' )


            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder'=> function(CategoryRepository $categoryRepository){
                    $qb=$categoryRepository->createQueryBuilder('c');
                    $qb ->where('c.status = 1');

                    return $qb;
                },
                'choice_label' => function(Category $c) {
                    if ($c ->getStatus()==1)
                    {
                        return $c->getNom();

                    }
                    return null;

                },

            ])




            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'autocomplete' => 'email',
                    'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-1/5 h-20 text-2xl outline-none',
                    'placeholder' => 'Email'
                ],
            ])

            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmation du mot de passe'),
            ))









            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])


;





    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
