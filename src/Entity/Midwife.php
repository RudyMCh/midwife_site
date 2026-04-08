<?php

namespace App\Entity;

use App\Repository\MidwifeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MidwifeRepository::class)]
class Midwife implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $lastname = '';

    /**
     * @Gedmo\Slug(fields={"firstname", "lastname"})
     */
    #[ORM\Column(length: 128, unique: true)]
    private string $slug = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $firstname = '';

    #[ORM\Column(type: 'string', length: 5000, nullable: true)]
    #[Assert\Length(min: 15, max: 5000, minMessage: 'Cette description devrait faire au moins 15 caractères', maxMessage: 'Cette description ne peut pas faire plus de 5000 caractères')]
    private ?string $aboutMe = null;

    #[ORM\Column(type: 'string', length: 5000, nullable: true)]
    #[Assert\Length(min: 15, max: 5000, minMessage: 'Cette description devrait faire au moins 15 caractères', maxMessage: 'Cette description ne peut pas faire plus de 5000 caractères')]
    private ?string $description = null;

    /**
     * @Assert\CssColor
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $backgroundColor1 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Url]
    private ?string $doctolibUrl = null;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    #[Assert\Regex('/^[0-9]{10}$/', message: 'Le numéro de téléphone {{ value }} ne doit contenir que des chiffres et faire une longueur de 10 caractères')]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Email(message: " l'email {{ value }} n'est pas valide")]
    private ?string $email = null;

    /** @var Collection<int, Path> */
    #[ORM\OneToMany(targetEntity: Path::class, mappedBy: 'midwife', orphanRemoval: true)]
    private Collection $paths;

    /** @var Collection<int, Degree> */
    #[ORM\OneToMany(targetEntity: Degree::class, mappedBy: 'midwife', orphanRemoval: true)]
    private Collection $degrees;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private ?MediaFile $picture = null;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private ?MediaFile $bgCard = null;

    /** @var Collection<int, Service> */
    #[ORM\ManyToMany(targetEntity: Service::class, mappedBy: 'midwives')]
    private Collection $services;

    #[ORM\Column(type: 'string', length: 70, nullable: true)]
    #[Assert\Length(max: 70, maxMessage: 'Le titre SEO ne doit pas dépasser 70 caractères')]
    private ?string $metaTitle = null;

    #[ORM\Column(type: 'string', length: 160, nullable: true)]
    #[Assert\Length(min: 80, max: 160, minMessage: 'Cette description pour les bots google devrait faire au moins 80 caractères', maxMessage: 'Cette description pour les bots google ne doit pas dépasser 160 caractères')]
    private ?string $metaDescription = null;

    /** @var Collection<int, MediaFile> */
    #[ORM\ManyToMany(targetEntity: MediaFile::class)]
    #[JoinTable(name: 'midwife_file')]
    private Collection $pictures;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private ?MediaFile $bgTitle = null;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    private ?MediaFile $pictureSelf = null;

    public function __construct()
    {
        $this->paths = new ArrayCollection();
        $this->degrees = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getAboutMe(): ?string
    {
        return $this->aboutMe;
    }

    public function setAboutMe(?string $aboutMe): self
    {
        $this->aboutMe = $aboutMe;

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

    public function getBackgroundColor1(): ?string
    {
        return $this->backgroundColor1;
    }

    public function setBackgroundColor1(?string $backgroundColor1): self
    {
        $this->backgroundColor1 = $backgroundColor1;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

    public function getDoctolibUrl(): ?string
    {
        return $this->doctolibUrl;
    }

    public function setDoctolibUrl(?string $doctolibUrl): self
    {
        $this->doctolibUrl = $doctolibUrl;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Path>
     */
    public function getPaths(): Collection
    {
        return $this->paths;
    }

    public function addPath(Path $path): self
    {
        if (!$this->paths->contains($path)) {
            $this->paths[] = $path;
            $path->setMidwife($this);
        }

        return $this;
    }

    public function removePath(Path $path): self
    {
        if ($this->paths->removeElement($path)) {
            if ($path->getMidwife() === $this) {
                $path->setMidwife(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Degree>
     */
    public function getDegrees(): Collection
    {
        return $this->degrees;
    }

    public function addDegree(Degree $degree): self
    {
        if (!$this->degrees->contains($degree)) {
            $this->degrees[] = $degree;
            $degree->setMidwife($this);
        }

        return $this;
    }

    public function removeDegree(Degree $degree): self
    {
        if ($this->degrees->removeElement($degree)) {
            if ($degree->getMidwife() === $this) {
                $degree->setMidwife(null);
            }
        }

        return $this;
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

    public function getBgCard(): ?MediaFile
    {
        return $this->bgCard;
    }

    public function setBgCard(?MediaFile $bgCard): self
    {
        $this->bgCard = $bgCard;

        return $this;
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
            $service->addMidwife($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            $service->removeMidwife($this);
        }

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

    public function getBgTitle(): ?MediaFile
    {
        return $this->bgTitle;
    }

    public function setBgTitle(?MediaFile $bgTitle): self
    {
        $this->bgTitle = $bgTitle;

        return $this;
    }

    public function getPictureSelf(): ?MediaFile
    {
        return $this->pictureSelf;
    }

    public function setPictureSelf(?MediaFile $pictureSelf): self
    {
        $this->pictureSelf = $pictureSelf;

        return $this;
    }
}
