<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChooseYear extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

//            ->add('year', EntityType::class, [
//                'class' => User::class,
//                'query_builder'=> function(UserRepository $userRepository){
//                    $qb=$userRepository->createQueryBuilder('c');
//                    $qb ->select('SUBSTRING(c.created_at,1,4) as year')
//                        ->groupBy('year')
//                        ->orderBy('year','ASC');
//
//                    return $qb;
//                },
//
            ->add('year',ChoiceType::class, [
                'choices' =>  array(
                    '2022' => '2022',
                    '2021' => '2021', )
                ,

                'expanded'  => false,
                'multiple'  => false,


            ])

//            ->add('category', EntityType::class, [
//                'class' => Category::class,
//                'query_builder'=> function(CategoryRepository $categoryRepository){
//                    $qb=$categoryRepository->createQueryBuilder('c');
//                    $qb ->where('c.status = 0');
//
//                    return $qb;
//                },
//                'choice_label' => function(Category $c) {
//                    if ($c ->getStatus()==0)
//                    {
//                        return $c->getNom();
//
//                    }
//                    return null;
//
//                },
//
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}



