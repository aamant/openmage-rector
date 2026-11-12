<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

use Aamant\OpenMageRector\Set\OpenMageSetList;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withSets([OpenMageSetList::OPENMAGE_20_19])
    ->withAutoloadPaths([__DIR__ . '/../../../Rector/ClassMethod/FormContainerSaveUrlOverrideRector/Source']);
