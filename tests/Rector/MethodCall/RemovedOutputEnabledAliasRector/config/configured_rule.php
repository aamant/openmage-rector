<?php

declare(strict_types=1);

use Aamant\OpenMageRector\Rector\MethodCall\RemovedOutputEnabledAliasRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([RemovedOutputEnabledAliasRector::class])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
