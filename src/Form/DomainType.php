<?php

namespace App\Form;

use App\Entity\Domain;
use App\Entity\Service;
use App\Form\Type\MediaFileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomainType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Désignation',
            ])
            ->add('titleBg', MediaFileType::class, [
                'label' => 'Image de background du titre',
            ])
            ->add('titleColorBg', ColorType::class, [
                'label' => 'Couleur de fond du backGround',
                'help' => 'Utilisé si aucune image de background n\'est sélectonné',
            ])
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'label' => 'Prestations',
                'multiple' => true,
                'required' => false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('metaTitle', TextType::class, [
                'label' => 'Titre SEO (meta title)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex : Suivi de grossesse a Quetigny — Cabinet de sage-femme',
                    'data-seo-min' => '50',
                    'data-seo-max' => '60',
                ],
                'help' => 'Affiche comme titre cliquable dans Google. Idealement 50-60 caracteres. Commencez par le mot-cle principal (la specialite ou la pathologie traitee).',
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'Description SEO (meta description)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Ex : Nos sages-femmes vous accompagnent pour le suivi de votre grossesse. Consultez et prenez rendez-vous en ligne sur Doctolib.',
                    'data-seo-min' => '120',
                    'data-seo-max' => '160',
                ],
                'help' => 'Texte affiche sous le titre dans Google. Entre 120 et 160 caracteres. Utilisez des verbes d\'action (decouvrir, consulter, prendre rendez-vous...).',
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Domain::class,
        ]);
    }
}
