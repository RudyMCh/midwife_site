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
                'label' => 'Titre pour le référencement de la page',
                'help' => 'Doit contenir le mot clé principal, ne pas dépasser 65 caractères (10 à 12 mots), être attractive'])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'Description pour le référencement de la page',
                'help' => '120 caractères maximum, il est recommandé d’employer des verbes d’action du type « découvrir », « télécharger », « créer », etc.).',
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
