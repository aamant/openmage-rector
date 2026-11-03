<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\ValueObject;

/**
 * Méthode dépréciée de $class remplacée par un appel statique, avec les mêmes arguments.
 */
final class MethodToStaticCall
{
    public function __construct(
        public readonly string $class,
        public readonly string $method,
        public readonly string $targetClass,
        public readonly string $targetMethod,
    ) {
    }
}
