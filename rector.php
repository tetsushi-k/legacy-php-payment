<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/modern/src',
        __DIR__ . '/modern/public/index.php',
    ])
    ->withPhpSets(php83: true)
    ->withSets([
        SetList::CODE_QUALITY,
        LevelSetList::UP_TO_PHP_83,
    ]);
