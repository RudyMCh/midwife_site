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
                'label' => 'Prénom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('aboutMe', TextareaType::class, [
                'label' => 'A propos de moi',
                'help' => 'Résumé pour la carte de présentation de la page d\'accueil.',
                'required' => false,
                'attr' => [
                    'rows' => 6,
                ],
            ])
            ->add('picture', MediaFileType::class, [
                'label' => 'Photo d\'identité',
                'help' => 'Photo pour les cartes de présentation.',
            ])
            ->add('bgCard', MediaFileType::class, [
                'label' => 'Photo d\'arrière plan de la carte',
                'help' => 'Photo d\'arrière plan des cartes de présentation individuel',
            ])
            ->add('backgroundColor1', ColorType::class, [
                'label' => 'Couleur principale',
                'help' => 'Couleur de fond de la carte Sage femme (top) et du titre de sa page si aucune photo n\'a été définie',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'help' => 'Description pour la page sage-femme.',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('bgTitle', MediaFileType::class, [
                'label' => 'Image d\'arrière plan du titre de la page',
                'help' => 'Photo de fond du titre de la page',
            ])
            ->add('pictureSelf', MediaFileType::class, [
                'label' => 'Photo principale de la page sage femme',
            ])
            ->add('pictures', MediaFileCollectionType::class, [
                'label' => 'Photos du carousel',
            ])
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'label' => 'Prestations',
                'help' => 'Les prestations effectuées par la sage femme.',
                'multiple' => true,
                'required' => false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'help' => '10 caractères',
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
                'label' => 'Titre SEO',
                'help' => '70 caractères maximum. Ex : Prenom Nom — Sage-femme a Quetigny.',
                'required' => false,
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'Description pour le referencement de la page',
                'help' => "80-160 caracteres. Privilegier des verbes d'action : decouvrir, prendre rendez-vous.",
                'required' => false,
                'attr' => ['rows' => 3],
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
