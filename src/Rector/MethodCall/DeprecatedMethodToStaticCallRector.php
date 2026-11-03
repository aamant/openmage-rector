<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\MethodCall;

use Aamant\OpenMageRector\Rector\AbstractDeprecatedRector;
use Aamant\OpenMageRector\ValueObject\MethodToStaticCall;
use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.19 marque #[Deprecated] des méthodes qui ne font qu'appeler une méthode statique de Mage
 * avec les mêmes arguments.
 */
final class DeprecatedMethodToStaticCallRector extends AbstractDeprecatedRector implements ConfigurableRectorInterface
{
    /**
     * @var MethodToStaticCall[]
     */
    protected array $methodsToStaticCalls = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remplace l\'appel d\'une méthode dépréciée du core par l\'appel statique qu\'elle enveloppe, si la méthode appelée est bien celle du core',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
$customer = $this->_getModel('customer/customer');
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$customer = Mage::getModel('customer/customer');
CODE_SAMPLE,
                    [new MethodToStaticCall('Mage_Customer_AccountController', '_getModel', 'Mage', 'getModel')]
                ),
            ]
        );
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        foreach ($configuration as $methodToStaticCall) {
            if (!$methodToStaticCall instanceof MethodToStaticCall) {
                throw new InvalidArgumentException('Configuration attendue : ' . MethodToStaticCall::class);
            }
        }

        $this->methodsToStaticCalls = $configuration;
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
        foreach ($this->methodsToStaticCalls as $methodToStaticCall) {
            if ($this->isName($node->name, $methodToStaticCall->method)
                && $this->callsDeprecatedMethod($node, $methodToStaticCall->class)
            ) {
                return new StaticCall(
                    $this->createName($methodToStaticCall->targetClass, $node),
                    $methodToStaticCall->targetMethod,
                    $node->args
                );
            }
        }

        return null;
    }
}
