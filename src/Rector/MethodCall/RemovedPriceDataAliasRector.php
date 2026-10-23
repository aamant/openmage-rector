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
 * OpenMage 20 retire addMinimalPrice() et addFinalPrice() de Mage_Catalog_Model_Resource_Product_Collection,
 * qui ne faisaient qu'appeler addPriceData(). Un second addPriceData() sans argument ne change pas la requête.
 */
final class RemovedPriceDataAliasRector extends AbstractRector
{
    private const ALIASES = ['addMinimalPrice', 'addFinalPrice'];

    private const TARGET = 'addPriceData';

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remplace addMinimalPrice() et addFinalPrice() par addPriceData() et fusionne les appels enchaînés',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$products->addMinimalPrice()
    ->addFinalPrice()
    ->addTaxPercents();
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$products->addPriceData()
    ->addTaxPercents();
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
        if (!$this->isRemovedAlias($node)) {
            return null;
        }

        $result = $node;
        while ($result->var instanceof MethodCall || $result->var instanceof NullsafeMethodCall) {
            $previous = $result->var;
            if ($this->isName($previous->name, self::TARGET)) {
                return $previous;
            }
            if (!$this->isNames($previous->name, self::ALIASES)) {
                break;
            }
            // Le type de retour d'un alias propre à une classe tierce est inconnu : la chaîne est laissée telle quelle.
            if (!$this->isRemovedAlias($previous)) {
                return null;
            }
            $result = $previous;
        }

        $result->name = new Identifier(self::TARGET);

        return $result;
    }

    private function isRemovedAlias(MethodCall|NullsafeMethodCall $node): bool
    {
        if (!$this->isNames($node->name, self::ALIASES) || $node->args !== []) {
            return false;
        }

        // Aucune classe du core 20 ne déclare ces méthodes : seule une classe qui les déclare elle-même est exclue.
        $methodName = $this->getName($node->name);
        foreach ($this->getType($node->var)->getObjectClassNames() as $className) {
            if ($this->reflectionProvider->hasClass($className)
                && $this->reflectionProvider->getClass($className)->hasNativeMethod($methodName)
            ) {
                return false;
            }
        }

        return true;
    }
}
