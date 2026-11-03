<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\ValueObject;

/**
 * Méthode dépréciée de $class remplacée par $newMethod, avec les mêmes arguments.
 */
final class MethodRename
{
    public function __construct(
        public readonly string $class,
        public readonly string $method,
        public readonly string $newMethod,
    ) {
    }
}
