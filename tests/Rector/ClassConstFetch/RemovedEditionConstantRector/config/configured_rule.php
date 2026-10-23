<?php

declare(strict_types=1);

use Aamant\OpenMageRector\Rector\ClassConstFetch\RemovedEditionConstantRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([RemovedEditionConstantRector::class])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
