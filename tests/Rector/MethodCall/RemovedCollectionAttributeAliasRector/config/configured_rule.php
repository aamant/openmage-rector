<?php

declare(strict_types=1);

use Aamant\OpenMageRector\Rector\MethodCall\RemovedCollectionAttributeAliasRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([RemovedCollectionAttributeAliasRector::class])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
