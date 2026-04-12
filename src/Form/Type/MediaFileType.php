<?php

namespace App\Form\Type;

use App\Entity\MediaFile;
use App\Repository\MediaFileRepository;
use App\Service\ImageUploadService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class MediaFileType extends AbstractType
{
    public function __construct(
        private readonly ImageUploadService $uploadService,
        private readonly MediaFileRepository $mediaFileRepository,
    ) {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $hint = $options['image_hint'];
        $fileAttr = ['accept' => 'image/*,video/mp4,application/pdf'];
        $constraints = [];

        if (\is_array($hint)) {
            if (!empty($hint['width'])) {
                $fileAttr['data-img-min-width'] = $hint['width'];
            }
            if (!empty($hint['height'])) {
                $fileAttr['data-img-min-height'] = $hint['height'];
            }
            if (!empty($hint['width']) || !empty($hint['height'])) {
                $constraintOptions = [
                    'minWidthMessage' => "Largeur insuffisante\u00a0: {{ width }}px reçus, {{ min_width }}px minimum requis.",
                    'minHeightMessage' => "Hauteur insuffisante\u00a0: {{ height }}px reçus, {{ min_height }}px minimum requis.",
                ];
                if (!empty($hint['width'])) {
                    $constraintOptions['minWidth'] = $hint['width'];
                }
                if (!empty($hint['height'])) {
                    $constraintOptions['minHeight'] = $hint['height'];
                }
                $constraints[] = new Image($constraintOptions);
            }
        }

        $builder
            ->add('existing_id', HiddenType::class, ['required' => false])
            ->add('new_file', FileType::class, [
                'required' => false,
                'label' => false,
                'attr' => $fileAttr,
                'constraints' => $constraints,
            ])
        ;

        $uploadService = $this->uploadService;
        $mediaFileRepository = $this->mediaFileRepository;
        $subDir = $options['sub_dir'];

        $builder->addModelTransformer(new CallbackTransformer(
            static fn (?MediaFile $mediaFile): array => ['existing_id' => $mediaFile?->getId(), 'new_file' => null],
            static function (array $data) use ($uploadService, $mediaFileRepository, $subDir): ?MediaFile {
                if (($data['new_file'] ?? null) instanceof UploadedFile) {
                    return $uploadService->upload($data['new_file'], $subDir);
                }
                if (!empty($data['existing_id'])) {
                    return $mediaFileRepository->find((int) $data['existing_id']);
                }

                return null;
            }
        ));
    }

    #[\Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $mediaFile = $form->getData();
        $view->vars['current_file'] = $mediaFile instanceof MediaFile ? $mediaFile : null;
        $view->vars['image_hint'] = $options['image_hint'];
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'sub_dir' => '',
            'data_class' => null,
            'image_hint' => null,
        ]);
        $resolver->setAllowedTypes('sub_dir', 'string');
        $resolver->setAllowedTypes('image_hint', ['null', 'array']);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'media_file';
    }
}
