<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
class Service implements \Stringable
{
    use SoftDeleteableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    #[ORM\Column(type: 'text')]
    private string $description = '';

    #[ORM\ManyToOne(targetEntity: Domain::class, inversedBy: 'services')]
    private ?Domain $domain = null;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(length: 128, unique: true)]
    private string $slug = '';

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private ?MediaFile $picture = null;

    /** @var Collection<int, Midwife> */
    #[ORM\ManyToMany(targetEntity: Midwife::class, inversedBy: 'services')]
    private Collection $midwives;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $position = null;

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


    public function getSlug(): string
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
     * @return Collection<int, Midwife>
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
