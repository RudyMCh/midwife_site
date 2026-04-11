<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Midwife;
use App\Form\Type\MediaFileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Ex : Les bienfaits de la sophrologie pendant la grossesse'],
            ])
            ->add('excerpt', TextareaType::class, [
                'label' => 'Résumé (extrait)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Court résumé affiché dans la liste des articles (160 caractères recommandés)',
                ],
                'help' => 'Affiché dans la liste des articles. Laissez vide pour utiliser le début du contenu.',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('featuredImage', MediaFileType::class, [
                'label' => 'Image à la une',
                'required' => false,
                'sub_dir' => 'blog',
            ])
            ->add('author', EntityType::class, [
                'class' => Midwife::class,
                'choice_label' => fn (Midwife $m) => $m->getFirstname().' '.$m->getLastname(),
                'label' => 'Auteure',
                'required' => false,
                'placeholder' => '— Aucune auteure sélectionnée —',
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'Publié',
                'required' => false,
                'help' => 'Cochez pour rendre l\'article visible sur le site.',
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label' => 'Date de publication',
                'required' => false,
                'widget' => 'single_text',
                'help' => 'Laissez vide pour utiliser la date actuelle lors de la publication.',
            ])
            ->add('metaTitle', TextType::class, [
                'label' => 'Meta title (SEO)',
                'required' => false,
                'attr' => ['placeholder' => 'Titre affiché dans les résultats Google (≤ 60 caractères)'],
                'help' => 'Laissez vide pour utiliser le titre de l\'article.',
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'Meta description (SEO)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Description affichée sous le titre dans Google (≤ 160 caractères)',
                ],
                'help' => 'Laissez vide pour utiliser le résumé de l\'article.',
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
