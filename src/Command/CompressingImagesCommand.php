<?php

namespace App\Command;

use App\Service\ImageUploadService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsCommand(name: 'compress:images', description: 'Compression des images du dossier public/uploads')]
class CompressingImagesCommand extends Command
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
        private readonly ImageUploadService $imageUploadService,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $_input, OutputInterface $output): int
    {
        $uploadsDir = $this->projectDir.'/public/uploads';
        $finder = new Finder();
        $count = 0;

        foreach ($finder->files()->in($uploadsDir) as $file) {
            $pathname = $file->getPathname();

            $ratioResize = $this->imageUploadService->getRatioResize($pathname);
            if (null !== $ratioResize) {
                $this->imageUploadService->resizeUploadedImage($pathname, $ratioResize);
            }

            $uploadedFile = new UploadedFile($pathname, $file->getFilename(), null, null, true);
            $ratioWeight = $this->imageUploadService->getRatioWeight($uploadedFile);
            if (null !== $ratioWeight) {
                $this->imageUploadService->compressUploadedImage($pathname, $ratioWeight);
            }

            if (null !== $ratioResize || null !== $ratioWeight) {
                ++$count;
            }
        }

        $output->writeln("Compressed/resized {$count} images.");

        return Command::SUCCESS;
    }
}
