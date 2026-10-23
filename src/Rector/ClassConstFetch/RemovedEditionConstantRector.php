<?php

declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\ClassConstFetch;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20 retire les constantes Mage::EDITION_* ; Mage::getEdition() renvoie toujours 'Community'.
 */
final class RemovedEditionConstantRector extends AbstractRector
{
    // Valeurs des constantes dans Mage 1.9 / OpenMage 19.
    private const VALUES = [
        'EDITION_COMMUNITY' => 'Community',
        'EDITION_ENTERPRISE' => 'Enterprise',
        'EDITION_PROFESSIONAL' => 'Professional',
        'EDITION_GO' => 'Go',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remplace les constantes Mage::EDITION_* par leur valeur',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$isEnterprise = Mage::getEdition() === Mage::EDITION_ENTERPRISE;
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$isEnterprise = Mage::getEdition() === 'Enterprise';
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassConstFetch::class];
    }

    /**
     * @param ClassConstFetch $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node->class instanceof Name || !$this->isName($node->class, 'Mage')) {
            return null;
        }

        $constantName = $this->getName($node->name);
        if ($constantName === null || !isset(self::VALUES[$constantName])) {
            return null;
        }

        return new String_(self::VALUES[$constantName]);
    }
}
