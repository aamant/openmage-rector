<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\Expression;

use Aamant\OpenMageRector\Rector\AbstractDeprecatedRector;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Expression;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.19 marque #[Deprecated] les addVisibleIn*FilterToCollection($collection) de
 * Mage_Catalog_Model_Product_Visibility, qui appellent $collection->setVisibility(self::getVisibleIn*Ids()).
 */
final class DeprecatedVisibilityFilterRector extends AbstractDeprecatedRector
{
    private const string VISIBILITY_CLASS = 'Mage_Catalog_Model_Product_Visibility';

    private const array METHODS = [
        'addVisibleInCatalogFilterToCollection' => 'getVisibleInCatalogIds',
        'addVisibleInSearchFilterToCollection' => 'getVisibleInSearchIds',
        'addVisibleInSiteFilterToCollection' => 'getVisibleInSiteIds',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remplace addVisibleIn{Catalog,Search,Site}FilterToCollection($collection) de Mage_Catalog_Model_Product_Visibility par $collection->setVisibility(), quand l\'appel forme une instruction seule',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$collection->setVisibility(Mage_Catalog_Model_Product_Visibility::getVisibleInCatalogIds());
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
    public function refactor(Node $node): ?Node
    {
        $call = $node->expr;
        if (!$call instanceof MethodCall && !$call instanceof NullsafeMethodCall) {
            return null;
        }

        $methodName = $this->getName($call->name);
        if ($methodName === null || !isset(self::METHODS[$methodName])
            || !$this->mayCallDeprecatedMethod($call, self::VISIBILITY_CLASS) || !$this->isDroppable($call->var)
        ) {
            return null;
        }

        $args = $this->getPositionalArgValues($call);
        if ($args === null || count($args) !== 1) {
            return null;
        }

        // getVisibleIn*Ids() sont statiques et le core les appelle par self:: : une surcharge n'y change rien.
        $node->expr = new MethodCall($args[0], 'setVisibility', [
            new Arg(new StaticCall($this->createName(self::VISIBILITY_CLASS, $node), self::METHODS[$methodName])),
        ]);

        return $node;
    }
}
