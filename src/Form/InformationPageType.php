<?php

namespace App\Form;

use App\Entity\InformationPage;
use App\Form\Type\MediaFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationPageType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'=>'Titre'
            ])
            ->add('titleBg', MediaFileType::class, [
                'label'=>'Background image du titre'
            ])
            ->add('legal', TextareaType::class, [
                'label' => 'Texte légal',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('coming', TextareaType::class, [
                'label' => 'Comment venir',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('price', TextareaType::class, [
                'label' => 'Tarifs pratiqués',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('links', TextareaType::class, [
                'label' => 'Liens utiles',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('mention')
            ->add('metaTitle', TextType::class, [
                'label' => "Titre SEO",
                'help' => "70 caracteres maximum.",
                'required' => false,
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => "Description pour le referencement",
                'help' => "80-160 caracteres.",
                'required' => false,
                'attr' => ['rows' => 3],
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InformationPage::class,
        ]);
    }
}
