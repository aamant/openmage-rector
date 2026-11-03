<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

use Aamant\OpenMageRector\Rector\MethodCall\DeprecatedMethodToStaticCallRector;
use Aamant\OpenMageRector\ValueObject\MethodToStaticCall;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withConfiguredRule(DeprecatedMethodToStaticCallRector::class, [
        new MethodToStaticCall('Mage_Core_Model_App', 'isInstalled', 'Mage', 'isInstalled'),
        new MethodToStaticCall('Mage_Customer_AccountController', '_getModel', 'Mage', 'getModel'),
    ])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
