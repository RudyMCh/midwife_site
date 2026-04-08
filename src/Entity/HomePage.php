<?php

namespace App\Entity;

use App\Repository\HomePageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

#[ORM\Entity(repositoryClass: HomePageRepository::class)]
class HomePage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title = '';

    #[ORM\Column(type: 'string', length: 500)]
    private string $catchphrase = '';

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $about = null;

    /** @var Collection<int, MediaFile> */
    #[ORM\ManyToMany(targetEntity: MediaFile::class)]
    #[JoinTable(name: 'home_page_file')]
    private Collection $pictures;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private ?MediaFile $backgroundImage1 = null;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private ?MediaFile $backgroundImage2 = null;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private ?MediaFile $titleBg = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $metaDescription = null;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCatchphrase(): ?string
    {
        return $this->catchphrase;
    }

    public function setCatchphrase(string $catchphrase): self
    {
        $this->catchphrase = $catchphrase;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    /**
     * @return Collection<int, MediaFile>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(MediaFile $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
        }

        return $this;
    }

    public function removePicture(MediaFile $picture): self
    {
        $this->pictures->removeElement($picture);

        return $this;
    }

    public function getBackgroundImage1(): ?MediaFile
    {
        return $this->backgroundImage1;
    }

    public function setBackgroundImage1(?MediaFile $backgroundImage1): self
    {
        $this->backgroundImage1 = $backgroundImage1;

        return $this;
    }

    public function getBackgroundImage2(): ?MediaFile
    {
        return $this->backgroundImage2;
    }

    public function setBackgroundImage2(?MediaFile $backgroundImage2): self
    {
        $this->backgroundImage2 = $backgroundImage2;

        return $this;
    }

    public function getTitleBg(): ?MediaFile
    {
        return $this->titleBg;
    }

    public function setTitleBg(?MediaFile $titleBg): self
    {
        $this->titleBg = $titleBg;

        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }
}
