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
                'label' => 'Titre',
            ])
            ->add('titleBg', MediaFileType::class, [
                'label' => 'Background image du titre',
            ])
            ->add('legal', TextareaType::class, [
                'label' => 'Texte legal',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('coming', TextareaType::class, [
                'label' => 'Comment venir',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('price', TextareaType::class, [
                'label' => 'Tarifs pratiques',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('links', TextareaType::class, [
                'label' => 'Liens utiles',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('mention')
            ->add('metaTitle', TextType::class, [
                'label' => 'Titre SEO (meta title)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex : Informations pratiques — Cabinet de sages-femmes a Quetigny',
                    'data-seo-min' => '50',
                    'data-seo-max' => '60',
                ],
                'help' => 'Affiche comme titre cliquable dans Google. Idealement 50-60 caracteres. Commencez par la nature de la page puis le nom du cabinet.',
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'Description SEO (meta description)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Ex : Horaires, tarifs et acces au cabinet de sages-femmes de Quetigny. Retrouvez toutes les informations pratiques pour votre consultation.',
                    'data-seo-min' => '120',
                    'data-seo-max' => '160',
                ],
                'help' => 'Texte affiche sous le titre dans Google. Entre 120 et 160 caracteres. Resumez le contenu de la page (horaires, tarifs, acces...).',
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
