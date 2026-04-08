<?php

namespace App\Services;

class Tools
{
    public function __construct(private readonly PropertyInspectorService $propertyInspector)
    {
    }

    /**
     * @param class-string $class
     * @return array<string>|null
     */
    public function getProperties(string $class): ?array
    {
        return $this->propertyInspector->getProperties($class);
    }
}
