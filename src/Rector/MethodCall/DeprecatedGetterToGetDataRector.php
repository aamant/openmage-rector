<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\MethodCall;

use Aamant\OpenMageRector\Rector\AbstractDeprecatedRector;
use Aamant\OpenMageRector\ValueObject\GetterToGetData;
use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.19 marque #[Deprecated] des accesseurs explicites qui ne font que lire une clé de _data.
 */
final class DeprecatedGetterToGetDataRector extends AbstractDeprecatedRector implements ConfigurableRectorInterface
{
    /**
     * @var GetterToGetData[]
     */
    protected array $gettersToGetData = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remplace un accesseur déprécié du core par getData() sur la même clé, si la méthode appelée est bien celle du core',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
$scope = (int) $attribute->getIsGlobal();
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$scope = (int) $attribute->getData('is_global');
CODE_SAMPLE,
                    [new GetterToGetData('Mage_Eav_Model_Entity_Attribute_Abstract', 'getIsGlobal', 'is_global')]
                ),
            ]
        );
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        foreach ($configuration as $getterToGetData) {
            if (!$getterToGetData instanceof GetterToGetData) {
                throw new InvalidArgumentException('Configuration attendue : ' . GetterToGetData::class);
            }
        }

        $this->gettersToGetData = $configuration;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class, StaticCall::class];
    }

    /**
     * @param MethodCall|NullsafeMethodCall|StaticCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->args !== []) {
            return null;
        }

        foreach ($this->gettersToGetData as $getterToGetData) {
            if ($this->isName($node->name, $getterToGetData->method)
                && $this->callsDeprecatedMethod($node, $getterToGetData->class)
            ) {
                $node->name = new Identifier('getData');
                $node->args = [new Arg(new String_($getterToGetData->key))];

                return $node;
            }
        }

        return null;
    }
}
