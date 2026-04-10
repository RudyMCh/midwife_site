<?php

namespace App\Entity;

use App\Repository\DegreeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DegreeRepository::class)]
class Degree
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $establishment = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title = '';

    #[ORM\Column(type: 'string', length: 4, nullable: true)]
    #[Assert\Regex('/^(19|20)[0-9]{2}$/', message: 'Une année est composée de 4 chiffres et est récente.')]
    private ?string $year = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $type = '';

    #[ORM\ManyToOne(targetEntity: Midwife::class, inversedBy: 'degrees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Midwife $midwife = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstablishment(): ?string
    {
        return $this->establishment;
    }

    public function setEstablishment(?string $establishment): self
    {
        $this->establishment = $establishment;

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

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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
}
