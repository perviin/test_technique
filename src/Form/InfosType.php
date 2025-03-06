<?php

namespace App\Form;

use App\Entity\Infos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userRank', TextType::class, [
                'label' => 'User Rank',
                'attr' => ['class' => 'form-control']
            ])
            ->add('victoire', IntegerType::class, [
                'label' => 'Victoire',
                'attr' => ['class' => 'form-control']
            ])
            ->add('defaite', IntegerType::class, [
                'label' => 'Défaite',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Infos::class,
        ]);
    }
}
