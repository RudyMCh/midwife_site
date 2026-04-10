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
        $builder
            ->add('existing_id', HiddenType::class, ['required' => false])
            ->add('new_file', FileType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['accept' => 'image/*,video/mp4,application/pdf'],
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
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'sub_dir' => '',
            'data_class' => null,
        ]);
        $resolver->setAllowedTypes('sub_dir', 'string');
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'media_file';
    }
}
