<?php

namespace App\Entity;

use App\Repository\InformationPageRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\MediaFile;

#[ORM\Entity(repositoryClass: InformationPageRepository::class)]
class InformationPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private string $legal = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $coming = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $price = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $links = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mention = null;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private ?MediaFile $titleBg = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 70, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(type: 'string', length: 160, nullable: true)]
    private ?string $metaDescription = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLegal(): ?string
    {
        return $this->legal;
    }

    public function setLegal(string $legal): self
    {
        $this->legal = $legal;

        return $this;
    }

    public function getComing(): ?string
    {
        return $this->coming;
    }

    public function setComing(?string $coming): self
    {
        $this->coming = $coming;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getLinks(): ?string
    {
        return $this->links;
    }

    public function setLinks(?string $links): self
    {
        $this->links = $links;

        return $this;
    }

    public function getMention(): ?string
    {
        return $this->mention;
    }

    public function setMention(?string $mention): self
    {
        $this->mention = $mention;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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
