<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\FuncCall;

use Aamant\OpenMageRector\Rector\AbstractDeprecatedRector;
use Aamant\OpenMageRector\ValueObject\FunctionToStaticCall;
use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.19 marque #[Deprecated] des fonctions globales de app/code/core/Mage/Core/functions.php
 * qui ne font qu'appeler une méthode statique.
 */
final class DeprecatedFunctionToStaticCallRector extends AbstractDeprecatedRector implements ConfigurableRectorInterface
{
    /**
     * @var FunctionToStaticCall[]
     */
    protected array $functionsToStaticCalls = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remplace une fonction globale dépréciée par l\'appel statique qu\'elle enveloppe',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
$order->setUpdatedAt(now());
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$order->setUpdatedAt(Varien_Date::now());
CODE_SAMPLE,
                    [new FunctionToStaticCall('now', 'Varien_Date', 'now')]
                ),
            ]
        );
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        foreach ($configuration as $functionToStaticCall) {
            if (!$functionToStaticCall instanceof FunctionToStaticCall) {
                throw new InvalidArgumentException('Configuration attendue : ' . FunctionToStaticCall::class);
            }
        }

        $this->functionsToStaticCalls = $configuration;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     */
    public function refactor(Node $node): ?Node
    {
        foreach ($this->functionsToStaticCalls as $functionToStaticCall) {
            if ($this->isGlobalFunctionCall($node, $functionToStaticCall->function)) {
                return new StaticCall(
                    $this->createName($functionToStaticCall->class, $node),
                    $functionToStaticCall->method,
                    $node->args
                );
            }
        }

        return null;
    }
}
