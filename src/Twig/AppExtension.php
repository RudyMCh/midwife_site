<?php

namespace App\Twig;

use App\Entity\Service;
use App\Repository\DomainRepository;
use App\Repository\MidwifeRepository;
use App\Services\PropertyInspectorService;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DomainRepository $domainRepository,
        private readonly MidwifeRepository $midwifeRepository,
        private readonly PropertyInspectorService $propertyInspector,
    ) {
    }

    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('truncate', $this->truncate(...)),
            new TwigFilter('mobilePhone', $this->mobilePhone(...)),
        ];
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('dynamicVariable', $this->dynamicVariable(...)),
            new TwigFunction('getTypes', $this->getTypes(...)),
            new TwigFunction('get_class', 'get_class'),
            new TwigFunction('is_array', 'is_array'),
            new TwigFunction('countElements', $this->countElements(...)),
            new TwigFunction('getMidwives', $this->getMidwives(...)),
            new TwigFunction('getDomains', $this->getDomains(...)),
        ];
    }

    /** @param class-string $entity */
    public function countElements(string $entity): int
    {
        return $this->entityManager->getRepository($entity)->count([]);
    }

    /**
     * Accès dynamique à une propriété d'entité via son nom.
     * Supporte la notation "Getter1;Getter2" pour les relations (ex: "Picture;Path").
     */
    public function dynamicVariable(object $el, string $field): mixed
    {
        if (str_contains($field, ';')) {
            [$part1, $part2] = explode(';', $field, 2);
            $intermediate = $el->{'get'.$part1}();

            return $intermediate !== null ? $intermediate->{'get'.$part2}() : '';
        }

        $value = $el->{'get'.$field}();

        if (is_array($value)) {
            return implode(', ', $value);
        }

        return $value;
    }

    public function truncate(mixed $value, int $length, string $after): string
    {
        return mb_substr((string) $value, 0, $length, 'UTF-8').$after;
    }

    public function mobilePhone(mixed $value): string
    {
        $array = str_split((string) $value);
        $newValue = '';
        $i = 0;
        foreach ($array as $letter) {
            if ($i !== 0 && $i % 2 !== 0) {
                $newValue .= $letter;
            } else {
                $newValue .= ' '.$letter;
            }
            ++$i;
        }

        return $newValue;
    }

    /** @param class-string $class */
    public function getTypes(string $class, string $prop): mixed
    {
        return $this->propertyInspector->getType($class, $prop);
    }

    /** @return array<int, \App\Entity\Domain> */
    public function getDomains(): array
    {
        return $this->domainRepository->findAll();
    }

    /** @return array<int, \App\Entity\Midwife> */
    public function getMidwives(): array
    {
        return $this->midwifeRepository->findAll();
    }

    /** @return array<int, \App\Entity\Midwife> */
    public function getMidwivesByService(Service $service): array
    {
        return $this->midwifeRepository->findByService($service);
    }
}
