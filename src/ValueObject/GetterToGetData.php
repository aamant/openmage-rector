<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\ValueObject;

/**
 * Accesseur sans argument déprécié de $class remplacé par getData('$key').
 */
final class GetterToGetData
{
    public function __construct(
        public readonly string $class,
        public readonly string $method,
        public readonly string $key,
    ) {
    }
}
