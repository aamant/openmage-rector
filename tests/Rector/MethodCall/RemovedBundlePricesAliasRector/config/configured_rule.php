<?php

declare(strict_types=1);

use Aamant\OpenMageRector\Rector\MethodCall\RemovedBundlePricesAliasRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([RemovedBundlePricesAliasRector::class])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
