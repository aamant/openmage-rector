<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

use Aamant\OpenMageRector\Rector\ClassConstFetch\DeprecatedClassConstantRector;
use Aamant\OpenMageRector\ValueObject\ClassConstantReplacement;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withConfiguredRule(DeprecatedClassConstantRector::class, [
        new ClassConstantReplacement('Mage_Core_Helper_Data', 'XML_PATH_DEV_LOG_FILE', 'Mage_Core_Helper_Log', 'XML_PATH_DEV_LOG_FILE'),
        new ClassConstantReplacement('Varien_File_Uploader', 'TMP_NAME_EMPTY', null, 'UPLOAD_ERR_NO_FILE'),
    ])
    ->withAutoloadPaths([__DIR__ . '/../Source']);
