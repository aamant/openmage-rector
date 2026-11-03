<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\ValueObject;

/**
 * Constante dépréciée de $class remplacée par $newClass::$newConstant, ou par la constante globale $newConstant si $newClass est null.
 */
final class ClassConstantReplacement
{
    public function __construct(
        public readonly string $class,
        public readonly string $constant,
        public readonly ?string $newClass,
        public readonly string $newConstant,
    ) {
    }
}
