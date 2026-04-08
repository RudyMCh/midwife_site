<?php

namespace App\Entity;

use App\Repository\PathRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PathRepository::class)]
class Path
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Midwife::class, inversedBy: 'paths')]
    #[ORM\JoinColumn(nullable: false)]
    private $midwife;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 4, nullable: true)]
    #[Assert\Regex('/^(19|20)[0-9]{2}$/', message: 'Une année est composée de 4 chiffres et est récente.')]
    private $start;

    #[ORM\Column(type: 'string', length: 4, nullable: true)]
    #[Assert\Regex('/^(19|20)[0-9]{2}$/', message: 'Une année est composée de 4 chiffres et est récente.')]
    private $end;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Regex('/^\w+/', message: 'Une ville ne peut pas contenir de chiffres.')]
    private $city;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMidwife(): ?Midwife
    {
        return $this->midwife;
    }

    public function setMidwife(?Midwife $midwife): self
    {
        $this->midwife = $midwife;

        return $this;
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

    public function getStart(): ?string
    {
        return $this->start;
    }

    public function setStart(?string $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?string
    {
        return $this->end;
    }

    public function setEnd(?string $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }
}
