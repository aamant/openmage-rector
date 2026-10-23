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
 * OpenMage 20 retire getPrices() et getPricesDependingOnTax() de Mage_Bundle_Model_Product_Price,
 * qui ne faisaient qu'appeler getTotalPrices() avec les mêmes arguments.
 */
final class RemovedBundlePricesAliasRector extends AbstractRector
{
    private const ALIASES = ['getPrices', 'getPricesDependingOnTax'];

    private const TARGET = 'getTotalPrices';

    private const PRICE_MODEL_CLASS = 'Mage_Catalog_Model_Product_Type_Price';

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Renomme getPrices() et getPricesDependingOnTax() du modèle de prix bundle en getTotalPrices()',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
[$minPrice, $maxPrice] = $product->getPriceModel()->getPrices($product);
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
[$minPrice, $maxPrice] = $product->getPriceModel()->getTotalPrices($product);
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
        if (!$this->isNames($node->name, self::ALIASES) || $node->args === []) {
            return null;
        }

        $methodName = $this->getName($node->name);
        $isPriceModel = $this->isPriceModelCall($node->var);
        foreach ($this->getType($node->var)->getObjectClassNames() as $className) {
            if (!$this->reflectionProvider->hasClass($className)) {
                continue;
            }
            $classReflection = $this->reflectionProvider->getClass($className);
            if ($classReflection->hasNativeMethod($methodName)) {
                return null;
            }
            if ($className === self::PRICE_MODEL_CLASS || $classReflection->isSubclassOf(self::PRICE_MODEL_CLASS)) {
                $isPriceModel = true;
            }
        }

        if (!$isPriceModel) {
            return null;
        }

        $node->name = new Identifier(self::TARGET);

        return $node;
    }

    // Le produit d'une boucle ou d'un template n'est souvent pas typé : getPriceModel() suffit à reconnaître l'appel.
    private function isPriceModelCall(Node $node): bool
    {
        return ($node instanceof MethodCall || $node instanceof NullsafeMethodCall)
            && $this->isName($node->name, 'getPriceModel');
    }
}
