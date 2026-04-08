<?php

namespace App\Form;

use App\Entity\Domain;
use App\Entity\Midwife;
use App\Entity\Service;
use App\Form\Type\MediaFileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'=> 'Nom'
            ])
//            ->add('position', IntegerType::class, [
//                'label'=> 'Position',
////                'data'=> $options['data']->getId() === null ?$this->serviceRepository->count([]) : $options['data']->getPosition()
//            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('picture', MediaFileType::class, [
                'label'=>'Image',
                'required'=>false
            ])
            ->add('domain', EntityType::class, [
                'class'=>Domain::class,
                'choice_label'=>'name',
                'label'=>'Domaine'
            ])
            ->add('midwives', EntityType::class, [
                'class'=>Midwife::class,
                'multiple'=>true,
                'required'=>false,
                'label'=>'Sage-femmes',
                'attr'=>[
                    'class'=>'select2'
                ]
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
