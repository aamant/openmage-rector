<?php

declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20 retire getCollectionAttribute() de Mage_Eav_Model_Config, qui lisait le cache d'attributs
 * puis appelait getAttribute() avec les mêmes arguments.
 */
final class RemovedCollectionAttributeAliasRector extends AbstractRector
{
    private const ALIAS = 'getCollectionAttribute';

    private const TARGET = 'getAttribute';

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Renomme getCollectionAttribute() de Mage_Eav_Model_Config en getAttribute()',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$status = Mage::getSingleton('eav/config')->getCollectionAttribute('catalog_product', 'status');
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$status = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'status');
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
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /**
     * @param MethodCall|NullsafeMethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, self::ALIAS) || count($node->args) !== 2) {
            return null;
        }

        // Mage::getSingleton('eav/config') n'est pas typé : seule une classe qui déclare la méthode est exclue.
        foreach ($this->getType($node->var)->getObjectClassNames() as $className) {
            if ($this->reflectionProvider->hasClass($className)
                && $this->reflectionProvider->getClass($className)->hasNativeMethod(self::ALIAS)
            ) {
                return null;
            }
        }

        $node->name = new Identifier(self::TARGET);

        return $node;
    }
}
