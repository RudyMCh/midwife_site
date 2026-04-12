<?php

namespace App\Twig;

use App\Entity\Office;
use App\Entity\Service;
use App\Repository\DomainRepository;
use App\Repository\MidwifeRepository;
use App\Repository\OfficeRepository;
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
        private readonly OfficeRepository $officeRepository,
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
            new TwigFunction('getMidwivesByService', $this->getMidwivesByService(...)),
            new TwigFunction('getOffice', $this->getOffice(...)),
        ];
    }

    /** @param class-string $entity */
    public function countElements(string $entity): int
    {
        return $this->entityManager->getRepository($entity)->count([]);
    }

    /**
     * Accès dynamique à une propriété d'entité via son nom.
     * 
     * Supporte :
     * - Préfixes get/is/has/can (ex: "Published" → isPublished())
     * - Chaînage illimité avec ";" (ex: "Picture;Path" ou "Author;Company;Name")
     * - Normalisation automatique de la casse
     * - Sécurité (méthodes inexistantes → null)
     */
    public function dynamicVariable(object $el, string $field): mixed
    {
        // Découpage du chaînage (on accepte aussi les points pour plus de flexibilité)
        $parts = preg_split('/[;.]/', $field);

        $current = $el;

        foreach ($parts as $part) {
            if ($current === null || !is_object($current)) {
                return ''; // ou null selon ton besoin d'affichage Twig
            }

            $current = $this->getValueFromProperty($current, trim($part));
        }

        $value = $current;

        // Gestion des tableaux / collections pour l'affichage (comportement proche de l'original)
        if (is_iterable($value) && !is_string($value)) {
            $items = [];
            foreach ($value as $item) {
                $items[] = $item !== null ? (string) $item : '';
            }
            return implode(', ', $items);
        }

        return $value ?? '';
    }

    /**
     * Méthode privée qui tente tous les préfixes possibles.
     */
    private function getValueFromProperty(object $object, string $property): mixed
    {
        // Normalisation : on force la première lettre en majuscule (peu importe comment l'utilisateur l'écrit)
        $property = ucfirst($property);

        $prefixes = ['get', 'is', 'has', 'can'];

        foreach ($prefixes as $prefix) {
            $method = $prefix . $property;

            if (method_exists($object, $method) && is_callable([$object, $method])) {
                return $object->{$method}();
            }
        }

        // Méthode inexistante → on retourne null au lieu de planter
        return null;
    }

    public function truncate(mixed $value, int $length, string $after): string
    {
        return mb_substr((string) $value, 0, $length, 'UTF-8').$after;
    }

    public function mobilePhone(mixed $value): string
    {
        $digits = (string) preg_replace('/\D/', '', (string) $value);

        return trim(chunk_split($digits, 2, ' '));
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

    public function getOffice(): ?Office
    {
        return $this->officeRepository->findOneBy([]);
    }

    /** @return array<int, \App\Entity\Midwife> */
    public function getMidwivesByService(Service $service): array
    {
        return $this->midwifeRepository->findByService($service);
    }
}
