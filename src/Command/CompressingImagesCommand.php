<?php

namespace App\Command;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'compress:images', description: 'Compression des images')]
class CompressingImagesCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->setHelp('Compression des images du dossier public/upload')
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $finder = new Finder();
        $count = 0;
        foreach ($finder->files()->in('public/uploads') as $image) {
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
        $max_width = 200;
        $max_height = 200;

        $info = getimagesize($file);
        if ($info === false) {
            return '';
        }
        [$width, $height, $image_type] = $info;

        $src = match ($image_type) {
            1 => imagecreatefromgif($file),
            2 => imagecreatefromjpeg($file),
            3 => imagecreatefrompng($file),
            default => false,
        };

        if ($src === false) {
            return '';
        }

        $x_ratio = $max_width / $width;
        $y_ratio = $max_height / $height;

        if ($width <= $max_width && $height <= $max_height) {
            $tn_width = $width;
            $tn_height = $height;
        } elseif ($x_ratio * $height < $max_height) {
            $tn_height = (int) ceil($x_ratio * $height);
            $tn_width = $max_width;
        } else {
            $tn_width = (int) ceil($y_ratio * $width);
            $tn_height = $max_height;
        }

        $tmp = imagecreatetruecolor($tn_width, $tn_height);

        if ($image_type === 1 || $image_type === 3) {
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
            if ($transparent !== false) {
                imagefilledrectangle($tmp, 0, 0, $tn_width, $tn_height, $transparent);
            }
        }
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);

        ob_start();
        match ($image_type) {
            1 => imagegif($tmp),
            2 => imagejpeg($tmp, null, 100),
            3 => imagepng($tmp, null, 0),
            default => null,
        };
        $final_image = ob_get_clean();

        return $final_image !== false ? $final_image : '';
    }

}