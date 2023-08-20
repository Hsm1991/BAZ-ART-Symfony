<?php

namespace App\Form;

use App\Entity\Res;
use App\Repository\SalleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResType extends AbstractType
{
    public  function  __construct(private SalleRepository $repo){

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $salleList = $this->repo->findAll();
        $salles = [];
        foreach ($salleList as $salle){
            $salles[] = intval($salle->getId());
        }
        $builder
            ->add('nomArtiste')
            ->add('dateRes')
            ->add('montant')
            ->add('Salle_id',ChoiceType::class,['choices' => $salles])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Res::class,
        ]);
    }
}
