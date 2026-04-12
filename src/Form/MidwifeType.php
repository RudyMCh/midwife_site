<?php

namespace App\Form;

use App\Entity\Midwife;
use App\Entity\Service;
use App\Form\Type\MediaFileCollectionType;
use App\Form\Type\MediaFileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MidwifeType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prenom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('aboutMe', TextareaType::class, [
                'label' => 'A propos de moi',
                'help' => 'Resume pour la carte de presentation de la page d\'accueil.',
                'required' => false,
                'attr' => [
                    'rows' => 6,
                ],
            ])
            ->add('picture', MediaFileType::class, [
                'label' => 'Photo d\'identite',
                'help' => 'Photo pour les cartes de presentation.',
                'image_hint' => ['label' => 'Photo de profil (bulle ronde)', 'width' => 400, 'height' => 400, 'ratio' => '1:1 carré'],
            ])
            ->add('bgCard', MediaFileType::class, [
                'label' => 'Photo d\'arriere plan de la carte',
                'help' => 'Photo d\'arriere plan des cartes de presentation individuel',
                'image_hint' => ['label' => 'Fond de carte sage-femme', 'width' => 800, 'height' => 800, 'ratio' => '1:1 carré ou portrait'],
            ])
            ->add('backgroundColor1', ColorType::class, [
                'label' => 'Couleur principale',
                'help' => 'Couleur de fond de la carte Sage femme (top) et du titre de sa page si aucune photo n\'a ete definie',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'help' => 'Description pour la page sage-femme.',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('bgTitle', MediaFileType::class, [
                'label' => 'Image d\'arriere plan du titre de la page',
                'help' => 'Photo de fond du titre de la page',
                'image_hint' => ['label' => 'Bannière de page (héros)', 'width' => 1920, 'height' => 600, 'ratio' => '16:5 paysage large'],
            ])
            ->add('pictureSelf', MediaFileType::class, [
                'label' => 'Photo principale de la page sage femme',
                'image_hint' => ['label' => 'Photo portrait de la page', 'width' => 600, 'height' => 800, 'ratio' => '3:4 portrait'],
            ])
            ->add('pictures', MediaFileCollectionType::class, [
                'label' => 'Photos du carousel',
                'entry_options' => [
                    'image_hint' => ['label' => 'Photo carousel sage-femme', 'width' => 1200, 'height' => 800, 'ratio' => '3:2 paysage'],
                ],
            ])
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'label' => 'Prestations',
                'help' => 'Les prestations effectuees par la sage femme.',
                'multiple' => true,
                'required' => false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Telephone',
                'help' => '10 caracteres',
                'required' => false,
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'required' => false,
            ])
            ->add('doctolibUrl', TextType::class, [
                'label' => 'Url Doctolib',
                'help' => 'Pour la prise de rendez vous sur Doctolib, l\'url de Doctolib complete',
            ])
            ->add('metaTitle', TextType::class, [
                'label' => 'Titre SEO (meta title)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex : Camille Dupont — Sage-femme a Quetigny | Suivi grossesse',
                    'data-seo-min' => '50',
                    'data-seo-max' => '60',
                ],
                'help' => 'Affiche comme titre cliquable dans Google. Idealement 50-60 caracteres. Format recommande : Prenom Nom — Sage-femme a [Ville].',
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'Description SEO (meta description)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Ex : Camille Dupont, sage-femme a Quetigny, vous accompagne pour le suivi de grossesse et la preparation a la naissance. Prenez rendez-vous en ligne.',
                    'data-seo-min' => '120',
                    'data-seo-max' => '160',
                ],
                'help' => 'Texte affiche sous le titre dans Google. Entre 120 et 160 caracteres. Mentionnez le prenom, la ville et les specialites. Ajoutez un appel a l\'action.',
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Midwife::class,
        ]);
    }
}
