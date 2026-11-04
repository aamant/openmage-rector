<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\Expression;

use Aamant\OpenMageRector\Rector\AbstractDeprecatedRector;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.19 marque #[Deprecated] addVisibleFilterToCollection() et addSaleableFilterToCollection()
 * de Mage_Catalog_Model_Product_Status, qui ne filtrent rien et renvoient $this.
 */
final class DeprecatedNoopProductStatusFilterRector extends AbstractDeprecatedRector
{
    private const string STATUS_CLASS = 'Mage_Catalog_Model_Product_Status';

    private const array METHODS = ['addVisibleFilterToCollection', 'addSaleableFilterToCollection'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Supprime les appels sans effet à addVisibleFilterToCollection() et addSaleableFilterToCollection() de Mage_Catalog_Model_Product_Status, quand ils forment une instruction seule',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
$this->setCollection($collection);
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$this->setCollection($collection);
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
        return [Expression::class];
    }

    /**
     * @param Expression $node
     */
    public function refactor(Node $node): ?int
    {
        $call = $node->expr;
        if (!$call instanceof MethodCall && !$call instanceof NullsafeMethodCall) {
            return null;
        }

        if (!$this->isNames($call->name, self::METHODS) || !$this->mayCallDeprecatedMethod($call, self::STATUS_CLASS)
            || !$this->isDroppable($call->var)
        ) {
            return null;
        }

        $args = $this->getPositionalArgValues($call);
        if ($args === null) {
            return null;
        }

        foreach ($args as $arg) {
            if (!$this->isDroppable($arg)) {
                return null;
            }
        }

        return NodeVisitor::REMOVE_NODE;
    }
}
