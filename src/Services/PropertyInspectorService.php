<?php

namespace App\Services;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;

class PropertyInspectorService
{
    private readonly PropertyInfoExtractor $propertyInfo;

    public function __construct()
    {
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();

        $this->propertyInfo = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor],
            [$phpDocExtractor],
            [$reflectionExtractor],
            [$reflectionExtractor]
        );
    }

    /**
     * @param class-string $class
     *
     * @return array<string>|null
     */
    public function getProperties(string $class): ?array
    {
        return $this->propertyInfo->getProperties($class);
    }

    /**
     * @param class-string $class
     */
    public function getType(string $class, string $property): ?Type
    {
        $types = $this->propertyInfo->getTypes($class, $property);

        return null !== $types && isset($types[0]) ? $types[0] : null;
    }
}
