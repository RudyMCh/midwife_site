<?php

namespace App\Tests\Twig;

use App\Entity\MediaFile;
use App\Twig\MediaExtension;
use PHPUnit\Framework\TestCase;

class MediaExtensionTest extends TestCase
{
    private MediaExtension $ext;

    protected function setUp(): void
    {
        $this->ext = new MediaExtension();
    }

    // -------------------------------------------------------------------------
    // mediaPath
    // -------------------------------------------------------------------------

    public function testMediaPathReturnsFallbackForNull(): void
    {
        $this->assertSame('/utils/no-picture.jpg', $this->ext->mediaPath(null));
    }

    public function testMediaPathReturnsCustomFallbackForNull(): void
    {
        $this->assertSame('/custom/fallback.jpg', $this->ext->mediaPath(null, '/custom/fallback.jpg'));
    }

    public function testMediaPathReturnsFilePath(): void
    {
        $file = $this->makeFile('/uploads', 'photo.jpg');

        $this->assertSame('/uploads/photo.jpg', $this->ext->mediaPath($file));
    }

    // -------------------------------------------------------------------------
    // mediaRender — images
    // -------------------------------------------------------------------------

    public function testMediaRenderReturnsImgTagForJpeg(): void
    {
        $file = $this->makeFile('/uploads', 'photo.jpg', alt: 'Belle photo');
        $html = $this->ext->mediaRender($file, 'my-class');

        $this->assertStringContainsString('<img', $html);
        $this->assertStringContainsString('src="/uploads/photo.jpg"', $html);
        $this->assertStringContainsString('class="my-class"', $html);
        $this->assertStringContainsString('alt="Belle photo"', $html);
    }

    public function testMediaRenderReturnsImgTagForPng(): void
    {
        $file = $this->makeFile('/uploads', 'image.png');
        $html = $this->ext->mediaRender($file);

        $this->assertStringContainsString('<img', $html);
    }

    public function testMediaRenderReturnsFallbackForNull(): void
    {
        $this->assertSame('', $this->ext->mediaRender(null));
        $this->assertSame('<span>vide</span>', $this->ext->mediaRender(null, '', '<span>vide</span>'));
    }

    public function testMediaRenderReturnsVideoTagForMp4(): void
    {
        $file = $this->makeFile('/uploads', 'video.mp4');
        $html = $this->ext->mediaRender($file, 'vid-class');

        $this->assertStringContainsString('<video', $html);
        $this->assertStringContainsString('class="vid-class"', $html);
        $this->assertStringContainsString('src="/uploads/video.mp4"', $html);
        $this->assertStringContainsString('type="video/mp4"', $html);
    }

    public function testMediaRenderReturnsEmbedTagForPdf(): void
    {
        $file = $this->makeFile('/uploads', 'doc.pdf');
        $html = $this->ext->mediaRender($file);

        $this->assertStringContainsString('<embed', $html);
        $this->assertStringContainsString('type="application/pdf"', $html);
    }

    public function testMediaRenderReturnsIframeForYouTube(): void
    {
        $file = $this->makeFile('/uploads', 'embed.html');
        $file->setIsIframe(true);
        $file->setVideoUrl('https://www.youtube.com/embed/abc123');

        $html = $this->ext->mediaRender($file);

        $this->assertStringContainsString('<iframe', $html);
        $this->assertStringContainsString('https://www.youtube.com/embed/abc123', $html);
    }

    public function testMediaRenderEscapesSpecialCharsInAttributes(): void
    {
        $file = $this->makeFile('/uploads', 'file.jpg', alt: '<script>alert(1)</script>');
        $html = $this->ext->mediaRender($file);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function testMediaRenderReturnsLinkForUnknownExtension(): void
    {
        $file = $this->makeFile('/uploads', 'archive.zip');
        $html = $this->ext->mediaRender($file);

        $this->assertStringContainsString('<a', $html);
        $this->assertStringContainsString('href="/uploads/archive.zip"', $html);
    }

    // -------------------------------------------------------------------------
    // mediaThumb
    // -------------------------------------------------------------------------

    public function testMediaThumbReturnsThumbImgForImage(): void
    {
        $file = $this->makeFile('/uploads', 'photo.jpg', alt: 'Portrait');
        $html = $this->ext->mediaThumb($file, 'thumb-class');

        $this->assertStringContainsString('<img', $html);
        $this->assertStringContainsString('src="/thumbs/photo.jpg"', $html);
        $this->assertStringContainsString('class="thumb-class"', $html);
        $this->assertStringContainsString('alt="Portrait"', $html);
    }

    public function testMediaThumbFallsBackToMediaRenderForNonImage(): void
    {
        $file = $this->makeFile('/uploads', 'doc.pdf');
        $html = $this->ext->mediaThumb($file);

        $this->assertStringContainsString('<embed', $html);
    }

    public function testMediaThumbReturnsFallbackForNull(): void
    {
        $this->assertSame('', $this->ext->mediaThumb(null));
    }

    // -------------------------------------------------------------------------
    // getFunctions registration
    // -------------------------------------------------------------------------

    public function testGetFunctionsRegistersExpectedNames(): void
    {
        $names = array_map(
            static fn(\Twig\TwigFunction $f) => $f->getName(),
            $this->ext->getFunctions()
        );

        $this->assertContains('media_path', $names);
        $this->assertContains('media_render', $names);
        $this->assertContains('media_thumb', $names);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeFile(string $directory, string $filename, ?string $alt = null): MediaFile
    {
        $file = new MediaFile();
        $file->setDirectory($directory);
        $file->setFilename($filename);
        if ($alt !== null) {
            $file->setAlt($alt);
        }

        return $file;
    }
}
