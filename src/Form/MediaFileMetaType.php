<?php

namespace App\Form;

use App\Entity\MediaFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaFileMetaType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => false,
            ])
            ->add('alt', TextType::class, [
                'label' => 'Texte alternatif (SEO)',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('isVideo', CheckboxType::class, [
                'label' => 'Fichier vidéo',
                'required' => false,
            ])
            ->add('isIframe', CheckboxType::class, [
                'label' => 'Intégration iframe',
                'required' => false,
            ])
            ->add('videoUrl', UrlType::class, [
                'label' => 'URL de la vidéo',
                'required' => false,
            ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => MediaFile::class]);
    }
}
