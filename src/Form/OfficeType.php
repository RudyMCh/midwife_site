<?php

namespace App\Form;

use App\Entity\Office;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficeType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code Postal',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('about', TextType::class, [
                'label' => 'A propos',
                'required' => false,
            ])
            ->add('urlGoogleMap', TextType::class, [
                'label' => 'Url GoogleMap',
                'help' => 'Url disponible dans google map dans l\'option "partager", ne pas mettre la balise iframe, uniquement l\'url (https:// ...)',
            ])
            ->add('latitude', TextType::class, [
                'help' => 'Coordonnées utilisées pour le bouton Waze',
            ])
            ->add('longitude', TextType::class, [
                'help' => 'Coordonnées utilisées pour le bouton Waze',
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Office::class,
        ]);
    }
}
