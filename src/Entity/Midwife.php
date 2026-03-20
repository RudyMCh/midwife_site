<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\JoinTable;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Moustache\FileManagerBundle\Entity\File;
use App\Repository\MidwifeRepository;

/**
 * @ORM\Entity(repositoryClass=MidwifeRepository::class)
 */
class Midwife
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
    private $lastname;

    /**
     * @Gedmo\Slug(fields={"firstname", "lastname"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     * @Assert\Length(
     *      min=15,
     *      max = 5000,
     *      minMessage = "Cette description devrait faire au moins 15 caractères",
     *      maxMessage = "Cette description ne peut pas faire plus de 5000 caractères"
     * )
     */
    private $aboutMe;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     * @Assert\Length(
     *      min=15,
     *      max = 5000,
     *      minMessage = "Cette description devrait faire au moins 15 caractères",
     *      maxMessage = "Cette description ne peut pas faire plus de 5000 caractères"
     * )
     */
    private $description;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\CssColor
     */
    private $backgroundColor1;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url
     */
    private $doctolibUrl;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Assert\Regex("/^[0-9]{10}$/", message = "Le numéro de téléphone {{ value }} ne doit contenir que des chiffres et faire une longueur de 10 caractères")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email(message = " l'email {{ value }} n'est pas valide")
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Path::class, mappedBy="midwife", orphanRemoval=true)
     */
    private $paths;

    /**
     * @ORM\OneToMany(targetEntity=Degree::class, mappedBy="midwife", orphanRemoval=true)
     */
    private $degrees;

    /**
     * @ORM\ManyToOne(targetEntity=File::class)
     */
    private $picture;

    /**
     * @ORM\ManyToOne(targetEntity=File::class)
     * @JoinTable(name="service_midwife")
     */
    private $bgCard;

    /**
     * @ORM\ManyToMany(targetEntity=Service::class, mappedBy="midwives")
     */
    private $services;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     * @Assert\Length(min = 80, max = 120, 
     *      minMessage = "Cette description pour les bots google devrait faire au moins 80 caractères",
     *      maxMessage = "Cette description pour les bots google ne doit pas dépasser 120 caractères")
     */
    private $metaDescription;

    /**
     * @ORM\ManyToMany(targetEntity=File::class)
     */
    private $pictures;

    /**
     * @ORM\ManyToOne(targetEntity=File::class)
     */
    private $bgTitle;

    /**
     * @ORM\ManyToOne(targetEntity=File::class)
     */
    private $pictureSelf;

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

    public function getSlug()
    {
        return $this->slug;
    }

    public function __toString():string
    {
        return $this->getFirstname(). ' '.$this->getLastname();
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
     * @return Collection|Path[]
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
            // set the owning side to null (unless already changed)
            if ($path->getMidwife() === $this) {
                $path->setMidwife(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Degree[]
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
            // set the owning side to null (unless already changed)
            if ($degree->getMidwife() === $this) {
                $degree->setMidwife(null);
            }
        }

        return $this;
    }

    public function getPicture(): ?File
    {
        return $this->picture;
    }

    public function setPicture(?File $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getBgCard(): ?File
    {
        return $this->bgCard;
    }

    public function setBgCard(?File $bgCard): self
    {
        $this->bgCard = $bgCard;

        return $this;
    }

    /**
     * @return Collection|Service[]
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

    public function getBgTitle(): ?File
    {
        return $this->bgTitle;
    }

    public function setBgTitle(?File $bgTitle): self
    {
        $this->bgTitle = $bgTitle;

        return $this;
    }

    public function getPictureSelf(): ?File
    {
        return $this->pictureSelf;
    }

    public function setPictureSelf(?File $pictureSelf): self
    {
        $this->pictureSelf = $pictureSelf;

        return $this;
    }
}
