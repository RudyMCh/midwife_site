<?php

namespace App\Twig;

use App\Entity\Service;
use App\Repository\DomainRepository;
use App\Repository\MidwifeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly DomainRepository $domainRepository, private readonly MidwifeRepository $midwifeRepository)
    {
    }
    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('truncate', $this->truncate(...)),
            new TwigFilter('mobilePhone', $this->mobilePhone(...)),

            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
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

        ];
    }
    /** @param class-string $entity */
    public function countElements(string $entity): int
    {
        return $this->entityManager->getRepository($entity)->count([]);
    }

    public function dynamicVariable(object $el, string $field): mixed
    {
        $getter = 'get'.$field;
        if(count(explode(';', (string) $field)) > 1) {
            $getter1 = 'get'.explode(';', (string) $field)[0];
            $getter2 = 'get'.explode(';', (string) $field)[1];
            $value = $el->$getter1() ? $el->$getter1()->$getter2() : '';
        } else {
            $value = $el->$getter();
        }
        if(is_array($value)) {
            $arrayValue = "";
            foreach ($value as $key => $item) {
                $arrayValue .= $item;
                if($key !== (count($value) - 1)) {
                    $arrayValue .= ", ";
                }
            }
            return $arrayValue;
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
            if ($i!== 0 && $i%2 !== 0)
            {
                $newValue.= $letter;
            }else{
                $newValue.= ' '.$letter;
            }
            $i++;
        }
        return $newValue;
    }
    /** @param class-string $class */
    public function getTypes(string $class, string $prop): mixed
    {
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();

        $listExtractors = [$reflectionExtractor];
        $typeExtractors = [$phpDocExtractor, $reflectionExtractor];
        $descriptionExtractors = [$phpDocExtractor];
        $accessExtractors = [$reflectionExtractor];
        $propertyInitializableExtractors = [$reflectionExtractor];

        $propertyInfo = new PropertyInfoExtractor(
            $listExtractors,
            $typeExtractors,
            $descriptionExtractors,
            $accessExtractors,
            $propertyInitializableExtractors
        );
        $types = $propertyInfo->getTypes($class, $prop);

        return $types !== null && isset($types[0]) ? $types[0] : null;
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
