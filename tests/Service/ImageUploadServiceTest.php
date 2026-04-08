<?php

namespace App\Tests\Service;

use App\Entity\MediaFile;
use App\Repository\MediaFileRepository;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ImageUploadServiceTest extends TestCase
{
    private string $uploadsDir;
    private string $thumbsDir;

    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $em;

    /** @var MediaFileRepository&MockObject */
    private MediaFileRepository $repository;

    private ImageUploadService $service;

    protected function setUp(): void
    {
        $this->uploadsDir = sys_get_temp_dir().'/sf_test_uploads_'.uniqid('', true);
        $this->thumbsDir = sys_get_temp_dir().'/sf_test_thumbs_'.uniqid('', true);
        mkdir($this->uploadsDir, 0777, true);
        mkdir($this->thumbsDir, 0777, true);

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(MediaFileRepository::class);

        $this->service = new ImageUploadService(
            $this->uploadsDir,
            $this->thumbsDir,
            new AsciiSlugger(),
            $this->em,
            $this->repository,
        );
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->uploadsDir);
        $this->removeDir($this->thumbsDir);
    }

    // -------------------------------------------------------------------------
    // getRatioWeight
    // -------------------------------------------------------------------------

    public function testGetRatioWeightReturnsNullForSmallFile(): void
    {
        $file = $this->createUploadedFile($this->createTempJpeg(100, 100), 'small.jpg', 100);
        $result = $this->callPrivate('getRatioWeight', [$file]);

        $this->assertNull($result);
    }

    public function testGetRatioWeightReturnsRatioForLargeFile(): void
    {
        // Simulate a 400 000 byte file (2× MAX_WEIGHT) → ratio = 50
        $file = $this->createMock(UploadedFile::class);
        $file->method('getSize')->willReturn(400_000);

        $result = $this->callPrivate('getRatioWeight', [$file]);

        $this->assertSame(50, $result);
    }

    public function testGetRatioWeightRespectsMinQuality(): void
    {
        // Very large file: 10 000 000 bytes → raw ratio = 2 → floored to MIN_QUALITY=20
        $file = $this->createMock(UploadedFile::class);
        $file->method('getSize')->willReturn(10_000_000);

        $result = $this->callPrivate('getRatioWeight', [$file]);

        $this->assertSame(20, $result);
    }

    // -------------------------------------------------------------------------
    // getRatioResize
    // -------------------------------------------------------------------------

    public function testGetRatioResizeReturnsNullForSmallImage(): void
    {
        $path = $this->createTempJpeg(800, 600);
        $result = $this->callPrivate('getRatioResize', [$path]);

        $this->assertNull($result);
        unlink($path);
    }

    public function testGetRatioResizeReturnsRatioForWideImage(): void
    {
        // 4000×500 → limited by width: 2000/4000 = 50%
        $path = $this->createTempJpeg(4_000, 500);
        $result = $this->callPrivate('getRatioResize', [$path]);

        $this->assertSame(50, $result);
        unlink($path);
    }

    public function testGetRatioResizeReturnsRatioForTallImage(): void
    {
        // 1000×2000 → limited by height: 1000/2000 = 50%
        $path = $this->createTempJpeg(1_000, 2_000);
        $result = $this->callPrivate('getRatioResize', [$path]);

        $this->assertSame(50, $result);
        unlink($path);
    }

    public function testGetRatioResizePicksSmallestRatio(): void
    {
        // 4000×4000 → ratioW=50%, ratioH=25% → min=25
        $path = $this->createTempJpeg(4_000, 4_000);
        $result = $this->callPrivate('getRatioResize', [$path]);

        $this->assertSame(25, $result);
        unlink($path);
    }

    // -------------------------------------------------------------------------
    // upload()
    // -------------------------------------------------------------------------

    public function testUploadCreatesMediaFileWithCorrectProperties(): void
    {
        $this->repository->method('findByFilename')->willReturn(null);
        $this->em->expects($this->once())->method('persist')
            ->with($this->isInstanceOf(MediaFile::class));
        $this->em->expects($this->once())->method('flush');

        $uploadedFile = $this->createUploadedFile(
            $this->createTempJpeg(400, 300),
            'ma-photo.jpg'
        );

        $result = $this->service->upload($uploadedFile);

        $this->assertInstanceOf(MediaFile::class, $result);
        $this->assertMatchesRegularExpression('/\.(jpe?g)$/', $result->getFilename());
        $this->assertStringContainsString('ma-photo', $result->getFilename());
        $this->assertSame('/uploads', $result->getDirectory());
    }

    public function testUploadWithSubDirSetsCorrectDirectory(): void
    {
        $this->repository->method('findByFilename')->willReturn(null);
        $this->em->method('persist');
        $this->em->method('flush');

        $uploadedFile = $this->createUploadedFile(
            $this->createTempJpeg(200, 200),
            'photo.jpg'
        );

        $result = $this->service->upload($uploadedFile, 'midwives');

        $this->assertSame('/uploads/midwives', $result->getDirectory());
        $this->assertDirectoryExists($this->uploadsDir.'/midwives');
    }

    public function testUploadCreatesThumbFile(): void
    {
        $this->repository->method('findByFilename')->willReturn(null);
        $this->em->method('persist');
        $this->em->method('flush');

        $uploadedFile = $this->createUploadedFile(
            $this->createTempJpeg(400, 300),
            'portrait.jpg'
        );

        $result = $this->service->upload($uploadedFile);

        $thumbPath = $this->thumbsDir.'/'.$result->getFilename();
        $this->assertFileExists($thumbPath);

        [$thumbWidth] = getimagesize($thumbPath);
        $this->assertSame(200, $thumbWidth);
    }

    public function testUploadReturnsExistingMediaFileIfFilenameAlreadyExists(): void
    {
        $existing = new MediaFile();
        $existing->setFilename('already-exists.jpg');
        $existing->setDirectory('/uploads');

        $this->repository->method('findByFilename')->willReturn($existing);
        $this->em->expects($this->never())->method('persist');
        $this->em->expects($this->never())->method('flush');

        $uploadedFile = $this->createUploadedFile(
            $this->createTempJpeg(100, 100),
            'already-exists.jpg'
        );

        $result = $this->service->upload($uploadedFile);

        $this->assertSame($existing, $result);
    }

    public function testUploadMovesFileToTargetDir(): void
    {
        $this->repository->method('findByFilename')->willReturn(null);
        $this->em->method('persist');
        $this->em->method('flush');

        $uploadedFile = $this->createUploadedFile(
            $this->createTempJpeg(200, 200),
            'test.jpg'
        );

        $result = $this->service->upload($uploadedFile);

        $this->assertFileExists($this->uploadsDir.'/'.$result->getFilename());
    }

    // -------------------------------------------------------------------------
    // makeThumb — aspect ratio
    // -------------------------------------------------------------------------

    public function testThumbPreservesAspectRatio(): void
    {
        $this->repository->method('findByFilename')->willReturn(null);
        $this->em->method('persist');
        $this->em->method('flush');

        // 400×200 → thumb width=200, height should be 100
        $uploadedFile = $this->createUploadedFile(
            $this->createTempJpeg(400, 200),
            'landscape.jpg'
        );

        $result = $this->service->upload($uploadedFile);

        [$w, $h] = getimagesize($this->thumbsDir.'/'.$result->getFilename());
        $this->assertSame(200, $w);
        $this->assertSame(100, $h);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Creates a real JPEG temp file using GD.
     */
    private function createTempJpeg(int $width, int $height): string
    {
        $path = tempnam(sys_get_temp_dir(), 'sf_img_');
        $img = imagecreatetruecolor($width, $height);
        $color = imagecolorallocate($img, 100, 150, 200);
        imagefilledrectangle($img, 0, 0, $width - 1, $height - 1, $color);
        imagejpeg($img, $path, 90);
        imagedestroy($img);

        return $path;
    }

    /**
     * Wraps a temp file as an UploadedFile (test mode bypasses is_uploaded_file check).
     */
    private function createUploadedFile(string $path, string $originalName, ?int $size = null): UploadedFile
    {
        return new UploadedFile($path, $originalName, 'image/jpeg', $size, true);
    }

    /**
     * Calls a private method via reflection.
     */
    private function callPrivate(string $method, array $args): mixed
    {
        $ref = new \ReflectionMethod($this->service, $method);

        return $ref->invoke($this->service, ...$args);
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (glob($dir.'/*') as $file) {
            is_dir($file) ? $this->removeDir($file) : unlink($file);
        }
        rmdir($dir);
    }
}
