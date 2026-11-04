<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

use Aamant\OpenMageRector\Rector\Expression\DeprecatedVisibilityFilterRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([DeprecatedVisibilityFilterRector::class])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
