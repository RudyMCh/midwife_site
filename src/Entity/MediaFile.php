<?php

namespace App\Entity;

use App\Repository\MediaFileRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MediaFileRepository::class)
 * @ORM\Table(name="file")
 * @UniqueEntity("filename")
 */
class MediaFile
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $filename;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $directory;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description = null;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private ?string $slug = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $alt = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isVideo = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isIframe = null;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private ?string $videoUrl = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getIsVideo(): ?bool
    {
        return $this->isVideo;
    }

    public function setIsVideo(?bool $isVideo): self
    {
        $this->isVideo = $isVideo;

        return $this;
    }

    public function getIsIframe(): ?bool
    {
        return $this->isIframe;
    }

    public function setIsIframe(?bool $isIframe): self
    {
        $this->isIframe = $isIframe;

        return $this;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(?string $videoUrl): self
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    public function getPath(): string
    {
        return $this->directory.'/'.$this->filename;
    }

    public function __toString(): string
    {
        return $this->getPath();
    }
}
