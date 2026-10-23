<?php

declare(strict_types=1);

use Aamant\OpenMageRector\Rector\MethodCall\RemovedPriceDataAliasRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([RemovedPriceDataAliasRector::class])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
