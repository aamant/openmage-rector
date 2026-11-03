<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

use Aamant\OpenMageRector\Rector\FuncCall\DeprecatedFunctionToStaticCallRector;
use Aamant\OpenMageRector\ValueObject\FunctionToStaticCall;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withConfiguredRule(DeprecatedFunctionToStaticCallRector::class, [
        new FunctionToStaticCall('now', 'Varien_Date', 'now'),
    ])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
