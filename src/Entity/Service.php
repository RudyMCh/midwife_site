<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\MediaFile;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\ManyToOne(targetEntity: Domain::class, inversedBy: 'services')]
    private $domain;

    /**
     * @Gedmo\Slug(fields={"name"})
     */
    #[ORM\Column(length: 128, unique: true)]
    private $slug;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private $picture;

    #[ORM\ManyToMany(targetEntity: Midwife::class, inversedBy: 'services')]
    private $midwives;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $position;

    public function __construct()
    {
        $this->midwives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    public function setDomain(?Domain $domain): self
    {
        $this->domain = $domain;

        return $this;
    }


    public function getSlug()
    {
        return $this->slug;
    }

    public function getPicture(): ?MediaFile
    {
        return $this->picture;
    }

    public function setPicture(?MediaFile $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection|Midwife[]
     */
    public function getMidwives(): Collection
    {
        return $this->midwives;
    }

    public function addMidwife(Midwife $midwife): self
    {
        if (!$this->midwives->contains($midwife)) {
            $this->midwives[] = $midwife;
        }

        return $this;
    }

    public function removeMidwife(Midwife $midwife): self
    {
        $this->midwives->removeElement($midwife);

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;

        
    }
    #[\Override]
    public function __toString(): string
    {
        return (string) $this->name;
    }
}
