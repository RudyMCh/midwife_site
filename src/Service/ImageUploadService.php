<?php

namespace App\Service;

use App\Entity\MediaFile;
use App\Repository\MediaFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploadService
{
    private const int MAX_WEIGHT = 200_000;
    private const int MAX_WIDTH = 2_000;
    private const int MAX_HEIGHT = 1_000;
    private const int MIN_QUALITY = 20;
    private const int THUMB_SIZE = 200;

    public function __construct(
        private readonly string $uploadsDir,
        private readonly string $thumbsDir,
        private readonly SluggerInterface $slugger,
        private readonly EntityManagerInterface $em,
        private readonly MediaFileRepository $mediaFileRepository,
    ) {
    }

    public function upload(UploadedFile $file, string $subDir = ''): MediaFile
    {
        $newFilename = $this->generateFilename($file);
        $targetDir = $this->uploadsDir.($subDir ? '/'.$subDir : '');

        $this->makeThumb($file, $newFilename);

        $ratioResize = $this->getRatioResize($file->getPathname());
        $ratioWeight = $this->getRatioWeight($file);

        if ($ratioResize !== null) {
            $this->resizeUploadedImage($file->getPathname(), $ratioResize);
        }

        if ($ratioWeight !== null) {
            $this->compressUploadedImage($file->getPathname(), $ratioWeight);
        }

        try {
            $file->move($targetDir, $newFilename);
        } catch (FileException $e) {
            throw new \RuntimeException('Le transfert du fichier a échoué : '.$e->getMessage(), 0, $e);
        }

        $directoryFromPublic = '/uploads'.($subDir ? '/'.$subDir : '');

        $existing = $this->mediaFileRepository->findByFilename($newFilename);
        if ($existing !== null) {
            return $existing;
        }

        $mediaFile = new MediaFile();
        $mediaFile->setFilename($newFilename);
        $mediaFile->setDirectory($directoryFromPublic);

        $this->em->persist($mediaFile);
        $this->em->flush();

        return $mediaFile;
    }

    private function generateFilename(UploadedFile $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = $this->slugger->slug($originalName);

        return $safeName.'-'.uniqid('', false).'.'.$file->guessExtension();
    }

    private function makeThumb(UploadedFile $file, string $newFilename): void
    {
        $dest = $this->thumbsDir.'/'.$newFilename;
        $pathname = $file->getPathname();
        $info = @getimagesize($pathname);

        if ($info === false) {
            return;
        }

        [$width, $height] = $info;
        $sourceImage = $this->createImageResource($pathname, $info['mime']);

        if ($sourceImage === null) {
            return;
        }

        $thumbHeight = (int) floor($height * (self::THUMB_SIZE / $width));
        $destImage = imagecreatetruecolor(self::THUMB_SIZE, $thumbHeight);

        // Preserve transparency for PNG/GIF
        if (in_array($info['mime'], ['image/png', 'image/gif'], true)) {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            if ($transparent !== false) {
                imagefilledrectangle($destImage, 0, 0, self::THUMB_SIZE, $thumbHeight, $transparent);
            }
        }

        imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, self::THUMB_SIZE, $thumbHeight, $width, $height);
        imagejpeg($destImage, $dest, 90);

        imagedestroy($sourceImage);
        imagedestroy($destImage);
    }

    private function getRatioWeight(UploadedFile $file): ?int
    {
        $weight = $file->getSize();
        if ($weight === false || $weight <= self::MAX_WEIGHT) {
            return null;
        }

        $ratio = (int) ((self::MAX_WEIGHT / $weight) * 100);

        return max($ratio, self::MIN_QUALITY);
    }

    private function getRatioResize(string $pathname): ?int
    {
        $info = @getimagesize($pathname);
        if ($info === false) {
            return null;
        }

        [$width, $height] = $info;
        if ($width <= self::MAX_WIDTH && $height <= self::MAX_HEIGHT) {
            return null;
        }

        $ratioW = self::MAX_WIDTH / $width;
        $ratioH = self::MAX_HEIGHT / $height;

        return (int) (min($ratioW, $ratioH) * 100);
    }

    private function resizeUploadedImage(string $pathname, int $ratio): void
    {
        $info = @getimagesize($pathname);
        if ($info === false) {
            return;
        }

        [$width, $height] = $info;
        $newWidth = (int) ($width * $ratio / 100);
        $newHeight = (int) ($height * $ratio / 100);

        $source = $this->createImageResource($pathname, $info['mime']);
        if ($source === null) {
            return;
        }

        $dest = imagecreatetruecolor($newWidth, $newHeight);

        if (in_array($info['mime'], ['image/png', 'image/gif'], true)) {
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
            if ($transparent !== false) {
                imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);
            }
        }

        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagejpeg($dest, $pathname, 95);

        imagedestroy($source);
        imagedestroy($dest);
    }

    private function compressUploadedImage(string $pathname, int $quality): void
    {
        $info = @getimagesize($pathname);
        if ($info === false) {
            return;
        }

        $image = $this->createImageResource($pathname, $info['mime']);
        if ($image === null) {
            return;
        }

        imagejpeg($image, $pathname, $quality);
        imagedestroy($image);
    }

    private function createImageResource(string $pathname, string $mime): ?\GdImage
    {
        $result = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($pathname),
            'image/png' => imagecreatefrompng($pathname),
            'image/gif' => imagecreatefromgif($pathname),
            default => null,
        };

        return $result instanceof \GdImage ? $result : null;
    }
}
