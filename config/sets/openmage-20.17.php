<?php

declare(strict_types=1);

use Aamant\OpenMageRector\Rector\ClassMethod\AdminhtmlIsAllowedReturnTypeRector;
use Aamant\OpenMageRector\Rector\MethodCall\RemovedBundlePricesAliasRector;
use Aamant\OpenMageRector\Rector\MethodCall\RemovedEscapeAliasRector;
use Aamant\OpenMageRector\Rector\MethodCall\RemovedPriceDataAliasRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([
        AdminhtmlIsAllowedReturnTypeRector::class,
        RemovedBundlePricesAliasRector::class,
        RemovedEscapeAliasRector::class,
        RemovedPriceDataAliasRector::class,
    ]);
};
