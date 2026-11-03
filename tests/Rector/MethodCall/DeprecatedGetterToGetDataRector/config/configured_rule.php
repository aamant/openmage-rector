<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

use Aamant\OpenMageRector\Rector\MethodCall\DeprecatedGetterToGetDataRector;
use Aamant\OpenMageRector\ValueObject\GetterToGetData;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withConfiguredRule(DeprecatedGetterToGetDataRector::class, [
        new GetterToGetData('Mage_Eav_Model_Entity_Attribute_Abstract', 'getIsGlobal', 'is_global'),
    ])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
