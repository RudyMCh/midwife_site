<?php

namespace App\Entity;

use App\Repository\OfficeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OfficeRepository::class)]
class Office
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: 'string', length: 5)]
    private string $zipcode = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $city = '';

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 2000, nullable: true)]
    private ?string $about = null;

    #[ORM\Column(type: 'string', length: 600, nullable: true)]
    #[Assert\NotBlank]
    private ?string $urlGoogleMap = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $longitude = null;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

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

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getUrlGoogleMap(): ?string
    {
        return $this->urlGoogleMap;
    }

    public function setUrlGoogleMap(?string $urlGoogleMap): self
    {
        $this->urlGoogleMap = $urlGoogleMap;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }
}
