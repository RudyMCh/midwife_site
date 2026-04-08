<?php

namespace App\Twig;

use App\Entity\MediaFile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MediaExtension extends AbstractExtension
{
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('media_path', $this->mediaPath(...)),
            new TwigFunction('media_render', $this->mediaRender(...), ['is_safe' => ['html']]),
            new TwigFunction('media_thumb', $this->mediaThumb(...), ['is_safe' => ['html']]),
        ];
    }

    public function mediaPath(?MediaFile $file, string $fallback = '/utils/no-picture.jpg'): string
    {
        if ($file === null) {
            return $fallback;
        }

        return $file->getPath();
    }

    public function mediaRender(?MediaFile $file, string $class = '', string $fallback = ''): string
    {
        if ($file === null) {
            return $fallback;
        }

        $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));

        if ($file->getIsIframe()) {
            return sprintf(
                '<iframe width="100%%" height="315" src="%s" class="%s" style="border:0"'
                .' allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
                htmlspecialchars($file->getVideoUrl() ?? ''),
                htmlspecialchars($class)
            );
        }

        return match ($ext) {
            'jpg', 'jpeg', 'png', 'gif', 'webp' => sprintf(
                '<img src="%s" class="%s" alt="%s">',
                htmlspecialchars($file->getPath()),
                htmlspecialchars($class),
                htmlspecialchars($file->getAlt() ?? '')
            ),
            'mp4', 'webm' => sprintf(
                '<video controls class="%s"><source src="%s" type="video/%s"></video>',
                htmlspecialchars($class),
                htmlspecialchars($file->getPath()),
                htmlspecialchars($ext)
            ),
            'pdf' => sprintf(
                '<embed src="%s" class="%s" type="application/pdf" style="border:0;width:100%%;height:100%%">',
                htmlspecialchars($file->getPath()),
                htmlspecialchars($class)
            ),
            default => sprintf(
                '<a href="%s" class="%s">%s</a>',
                htmlspecialchars($file->getPath()),
                htmlspecialchars($class),
                htmlspecialchars($file->getFilename())
            ),
        };
    }

    public function mediaThumb(?MediaFile $file, string $class = '', string $fallback = ''): string
    {
        if ($file === null) {
            return $fallback;
        }

        $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            return sprintf(
                '<img src="/thumbs/%s" class="%s" alt="%s">',
                htmlspecialchars($file->getFilename()),
                htmlspecialchars($class),
                htmlspecialchars($file->getAlt() ?? '')
            );
        }

        return $this->mediaRender($file, $class, $fallback);
    }
}
