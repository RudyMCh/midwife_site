<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonySetList;

/**
 * Migration Symfony 5.4 → 7.4 / PHP 7.4 → 8.4
 *
 * Ordre d'exécution recommandé :
 *
 *   PASSE 1 — PHP
 *     vendor/bin/rector process --config rector.php --dry-run   # vérifier
 *     vendor/bin/rector process --config rector.php             # appliquer
 *
 *   PASSE 2 — Annotations → Attributes (Symfony + Doctrine)
 *     Décommenter la section "PASSE 2", commenter "PASSE 1"
 *
 *   PASSE 3 — Symfony 6.x upgrades
 *     Décommenter la section "PASSE 3"
 *
 *   PASSE 4 — Symfony 7.x upgrades
 *     Décommenter la section "PASSE 4"
 *
 *   Après chaque passe : valider avec PHPStan
 *     vendor/bin/phpstan analyse --level=8
 */
return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/lib',
    ])
    ->withSkip([
        __DIR__.'/src/Kernel.php',
        __DIR__.'/vendor',
        __DIR__.'/var',
    ])

    // -------------------------------------------------------------------------
    // PASSE 1 — Modernisation PHP 8.x
    // Convertit : propriétés typées, promoted properties, match, nullsafe, etc.
    // -------------------------------------------------------------------------
    ->withSets([
        LevelSetList::UP_TO_PHP_84,
    ])

    // -------------------------------------------------------------------------
    // PASSE 2 — Annotations → Attributes PHP 8
    // (décommenter après la passe 1)
    // -------------------------------------------------------------------------
    // ->withSets([
    //     SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
    //     DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
    // ])

    // -------------------------------------------------------------------------
    // PASSE 3 — Symfony 6.x upgrades
    // (décommenter après la passe 2)
    // -------------------------------------------------------------------------
    // ->withSets([
    //     SymfonySetList::SYMFONY_60,
    //     SymfonySetList::SYMFONY_61,
    //     SymfonySetList::SYMFONY_62,
    //     SymfonySetList::SYMFONY_63,
    //     SymfonySetList::SYMFONY_64,
    // ])

    // -------------------------------------------------------------------------
    // PASSE 4 — Symfony 7.x upgrades
    // (décommenter après la passe 3)
    // -------------------------------------------------------------------------
    // ->withSets([
    //     SymfonySetList::SYMFONY_70,
    //     SymfonySetList::SYMFONY_71,
    //     SymfonySetList::SYMFONY_72,
    //     SymfonySetList::SYMFONY_73,
    //     SymfonySetList::SYMFONY_74,
    // ])
;
