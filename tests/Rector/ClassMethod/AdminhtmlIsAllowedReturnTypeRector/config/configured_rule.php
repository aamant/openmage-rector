<?php

declare(strict_types=1);

use Aamant\OpenMageRector\Rector\ClassMethod\AdminhtmlIsAllowedReturnTypeRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([AdminhtmlIsAllowedReturnTypeRector::class])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
