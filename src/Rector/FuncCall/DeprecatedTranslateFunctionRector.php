<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\FuncCall;

use Aamant\OpenMageRector\Rector\AbstractDeprecatedRector;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.19 marque #[Deprecated] la fonction globale __(), qui appelle le traducteur sans portée de module.
 */
final class DeprecatedTranslateFunctionRector extends AbstractDeprecatedRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remplace __() par l\'appel au traducteur qu\'elle enveloppe, à comportement identique',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$label = __('Status: %s', $status);
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$label = Mage::app()->getTranslator()->translate(['Status: %s', $status]);
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
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isGlobalFunctionCall($node, '__')) {
            return null;
        }

        $items = [];
        foreach ($node->getArgs() as $arg) {
            if ($arg->name !== null) {
                return null;
            }

            $items[] = new ArrayItem($arg->value, null, false, [], $arg->unpack);
        }

        $translator = new MethodCall(new StaticCall($this->createName('Mage', $node), 'app'), 'getTranslator');

        return new MethodCall($translator, 'translate', [
            new Arg(new Array_($items, [AttributeKey::KIND => Array_::KIND_SHORT])),
        ]);
    }
}
