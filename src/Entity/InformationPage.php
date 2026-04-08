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
    private $id;

    #[ORM\Column(type: 'text')]
    private $legal;

    #[ORM\Column(type: 'text', nullable: true)]
    private $coming;

    #[ORM\Column(type: 'text', nullable: true)]
    private $price;

    #[ORM\Column(type: 'text', nullable: true)]
    private $links;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $mention;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private $titleBg;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $title;

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
}
