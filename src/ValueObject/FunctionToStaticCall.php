<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\ValueObject;

/**
 * Fonction globale dépréciée remplacée par un appel statique, avec les mêmes arguments.
 */
final class FunctionToStaticCall
{
    public function __construct(
        public readonly string $function,
        public readonly string $class,
        public readonly string $method,
    ) {
    }
}
