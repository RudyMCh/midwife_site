<?php

namespace App\Form;

use App\Entity\HomePage;
use App\Form\Type\MediaFileCollectionType;
use App\Form\Type\MediaFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomePageType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('catchphrase', TextType::class, [
                'label' => 'Phrase d\'accroche',
            ])
            ->add('about', TextareaType::class, [
                'label' => 'A propos',
            ])
            ->add('pictures', MediaFileCollectionType::class)
            ->add('titleBg', MediaFileType::class, [
                'label' => 'Barre titre',
                'help' => 'Image de background du titre de la page',
            ])
            ->add('backgroundImage1', MediaFileType::class, [
                'label' => 'Image 1',
            ])
            ->add('backgroundImage2', MediaFileType::class, [
                'label' => 'Image 2',
            ])
            ->add('metaTitle', TextType::class, [
                'label' => 'Titre SEO (meta title)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex : Cabinet de sages-femmes a Quetigny — Suivi grossesse et gynecologie',
                    'data-seo-min' => '50',
                    'data-seo-max' => '60',
                ],
                'help' => 'Affiche comme titre cliquable dans Google. Idealement 50-60 caracteres. Incluez la ville et la specialite principale.',
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'Description SEO (meta description)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Ex : Cabinet de sages-femmes a Quetigny. Suivi de grossesse, gynecologie et preparation a la naissance. Prenez rendez-vous sur Doctolib.',
                    'data-seo-min' => '120',
                    'data-seo-max' => '160',
                ],
                'help' => 'Texte affiche sous le titre dans Google. Entre 120 et 160 caracteres. Mentionnez la ville, les specialites et un appel a l\'action.',
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HomePage::class,
        ]);
    }
}
