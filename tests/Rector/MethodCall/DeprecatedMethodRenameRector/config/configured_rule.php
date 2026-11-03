<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

use Aamant\OpenMageRector\Rector\MethodCall\DeprecatedMethodRenameRector;
use Aamant\OpenMageRector\ValueObject\MethodRename;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withConfiguredRule(DeprecatedMethodRenameRector::class, [
        new MethodRename('Mage_Adminhtml_Block_Widget_Grid', 'getGridUrl', 'getAbsoluteGridUrl'),
        new MethodRename('Varien_File_Uploader', 'chechAllowedExtension', 'checkAllowedExtension'),
    ])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
