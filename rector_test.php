<?php

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;

return RectorConfig::configure()
    ->withPhpSets(php84: true)
    ->withPaths([__DIR__.'/src/Entity/MediaFile.php'])
    ->withConfiguredRule(AnnotationToAttributeRector::class, [
        new AnnotationToAttribute('Doctrine\\ORM\\Mapping\\Entity'),
        new AnnotationToAttribute('Doctrine\\ORM\\Mapping\\Column'),
    ]);
