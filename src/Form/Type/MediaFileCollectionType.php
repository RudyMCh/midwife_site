<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaFileCollectionType extends AbstractType
{
    #[\Override]
    public function getParent(): string
    {
        return CollectionType::class;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => MediaFileType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'prototype_name' => '__name__',
            'delete_empty' => false,
            'by_reference' => false,
            'error_bubbling' => false,
        ]);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'media_file_collection';
    }
}
