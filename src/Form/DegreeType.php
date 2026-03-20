<?php

namespace App\Form;

use App\Entity\Degree;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DegreeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('establishment', TextType::class, [
                'label'=> 'Etablissement'
            ])
            ->add('title', TextType::class, [
                'label'=>'Titre du diplôme'
            ])
            ->add('year', TextType::class, [
                'label'=>'Année d\'obtention'
            ])
            ->add('type', TextType::class, [
                'label'=>'Qualité du diplôme'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Degree::class,
        ]);
    }
}
