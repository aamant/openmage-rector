<?php

declare(strict_types=1);

use Aamant\OpenMageRector\Rector\MethodCall\RemovedEscapeAliasRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([RemovedEscapeAliasRector::class])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
