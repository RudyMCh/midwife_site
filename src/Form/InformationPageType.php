<?php

namespace App\Form;

use App\Entity\InformationPage;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Moustache\FileManagerBundle\Form\MoustacheFileType\MoustacheFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'=>'Titre'
            ])
            ->add('titleBg', MoustacheFileType::class, [
                'label'=>'Background image du titre'
            ])
            ->add('legal', CKEditorType::class, [
                'label'=>'Texte légal'
            ])
            ->add('coming', CKEditorType::class, [
                'label'=>'Comment venir'
            ])
            ->add('price', CKEditorType::class, [
                'label'=>'Tarifs pratiqués'
            ])
            ->add('links', CKEditorType::class, [
                'label'=>'Liens utiles'
            ])
            ->add('mention')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InformationPage::class,
        ]);
    }
}
