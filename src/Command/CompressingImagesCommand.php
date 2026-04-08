<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;

#[AsCommand(name: 'compress:images', description: 'Compression des images')]
class CompressingImagesCommand extends Command
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->setHelp('Compression des images du dossier public/uploads');
    }

    #[\Override]
    protected function execute(InputInterface $_input, OutputInterface $output): int
    {
        $uploadsDir = $this->projectDir.'/public/uploads';
        $finder = new Finder();
        $count = 0;

        foreach ($finder->files()->in($uploadsDir) as $image) {
            $scaledImage = $this->scaleImageFileToBlob($image->getPathname());
            if ($scaledImage !== '') {
                file_put_contents($image->getPathname(), $scaledImage);
                ++$count;
            }
        }

        $output->writeln("Compressed {$count} images.");

        return Command::SUCCESS;
    }

    private function scaleImageFileToBlob(string $file): string
    {
        $maxWidth = 200;
        $maxHeight = 200;

        $info = getimagesize($file);
        if ($info === false) {
            return '';
        }

        [$width, $height, $imageType] = $info;

        $src = match ($imageType) {
            1 => imagecreatefromgif($file),
            2 => imagecreatefromjpeg($file),
            3 => imagecreatefrompng($file),
            default => false,
        };

        if ($src === false) {
            return '';
        }

        $xRatio = $maxWidth / $width;
        $yRatio = $maxHeight / $height;

        if ($width <= $maxWidth && $height <= $maxHeight) {
            $tnWidth = $width;
            $tnHeight = $height;
        } elseif ($xRatio * $height < $maxHeight) {
            $tnHeight = (int) ceil($xRatio * $height);
            $tnWidth = $maxWidth;
        } else {
            $tnWidth = (int) ceil($yRatio * $width);
            $tnHeight = $maxHeight;
        }

        $tmp = imagecreatetruecolor($tnWidth, $tnHeight);

        if ($imageType === 1 || $imageType === 3) {
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
            if ($transparent !== false) {
                imagefilledrectangle($tmp, 0, 0, $tnWidth, $tnHeight, $transparent);
            }
        }

        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tnWidth, $tnHeight, $width, $height);

        ob_start();
        match ($imageType) {
            1 => imagegif($tmp),
            2 => imagejpeg($tmp, null, 100),
            3 => imagepng($tmp, null, 0),
            default => null,
        };
        $finalImage = ob_get_clean();

        return $finalImage !== false ? $finalImage : '';
    }
}
