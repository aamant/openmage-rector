<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

use Aamant\OpenMageRector\Rector\Expression\DeprecatedAddStatusToHistoryRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([DeprecatedAddStatusToHistoryRector::class])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
