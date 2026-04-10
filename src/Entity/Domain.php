<?php

namespace App\Entity;

use App\Repository\DomainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: DomainRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
class Domain implements \Stringable
{
    use TimestampableEntity;
    use SoftDeleteableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    /** @var Collection<int, Service> */
    #[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'domain', cascade: ['persist', 'remove'])]
    private Collection $services;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private ?MediaFile $titleBg = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $titleColorBg = null;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(length: 128, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $metaDescription = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setDomain($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getDomain() === $this) {
                $service->setDomain(null);
            }
        }

        return $this;
    }

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->getName();
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

    public function getTitleColorBg(): ?string
    {
        return $this->titleColorBg;
    }

    public function setTitleColorBg(?string $titleColorBg): self
    {
        $this->titleColorBg = $titleColorBg;

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
