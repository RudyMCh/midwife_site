<?php

namespace App\Entity;

use App\Repository\HomePageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Moustache\FileManagerBundle\Entity\File;

/**
 * @ORM\Entity(repositoryClass=HomePageRepository::class)
 */
class HomePage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $catchphrase;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $about;

    /**
     * @ORM\ManyToMany(targetEntity=File::class)
     */
    private $pictures;

    /**
     * @ORM\ManyToOne(targetEntity=File::class)
     */
    private $backgroundImage1;

    /**
     * @ORM\ManyToOne(targetEntity=File::class)
     */
    private $backgroundImage2;

    /**
     * @ORM\ManyToOne(targetEntity=File::class)
     */
    private $titleBg;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $metaTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $metaDescription;

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
     * @return Collection|File[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(File $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
        }

        return $this;
    }

    public function removePicture(File $picture): self
    {
        $this->pictures->removeElement($picture);

        return $this;
    }

    public function getBackgroundImage1(): ?File
    {
        return $this->backgroundImage1;
    }

    public function setBackgroundImage1(?File $backgroundImage1): self
    {
        $this->backgroundImage1 = $backgroundImage1;

        return $this;
    }

    public function getBackgroundImage2(): ?File
    {
        return $this->backgroundImage2;
    }

    public function setBackgroundImage2(?File $backgroundImage2): self
    {
        $this->backgroundImage2 = $backgroundImage2;

        return $this;
    }

    public function getTitleBg(): ?File
    {
        return $this->titleBg;
    }

    public function setTitleBg(?File $titleBg): self
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
